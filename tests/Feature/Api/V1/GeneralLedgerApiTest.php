<?php

namespace Tests\Feature\Api\V1;

use App\Models\ApiClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\NullHandler;
use Tests\TestCase;

/**
 * Feature tests for the General Ledger API.
 *
 * Every test runs inside a transaction that is rolled back, so it is safe
 * against a development database. Fixture vouchers are dated in 2099 to
 * stay clear of real data.
 */
class GeneralLedgerApiTest extends TestCase
{
    use DatabaseTransactions;

    const BASE = '/api/v1/gl';

    /** @var string */
    protected $key;

    /** @var ApiClient */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        config(['logging.channels.api' => ['driver' => 'monolog', 'handler' => NullHandler::class]]);

        list($this->client, $this->key) = ApiClient::issue('phpunit', ['gl.read']);
    }

    protected function api($uri, $key = null)
    {
        return $this->withHeaders(['Authorization' => 'Bearer ' . ($key ?: $this->key)])->getJson(self::BASE . $uri);
    }

    /**
     * Two L5 accounts plus one posted and one prepared voucher in Jan 2099.
     *
     * @return array [accountA, accountB, postedVoucherId, preparedVoucherId]
     */
    protected function seedLedger()
    {
        $accounts = DB::table('fin_chart_of_accounts')->where('level', 'L5')->orderBy('code')->limit(2)->pluck('id')->all();
        if (count($accounts) < 2) {
            $this->markTestSkipped('Needs at least two L5 accounts in fin_chart_of_accounts.');
        }
        list($a, $b) = $accounts;

        $posted = $this->voucher(5, 9001, '2099-01-15', 3, [[$a, 100.50, 0], [$b, 0, 100.50]]);
        $prepared = $this->voucher(1, 9002, '2099-01-20', 1, [[$a, 40, 0], [$b, 0, 40]]);

        return [$a, $b, $posted, $prepared];
    }

    protected function voucher($type, $series, $date, $status, array $lines)
    {
        $id = DB::table('fin_general_ledgers')->insertGetId([
            'txn_type' => $type, 'txn_series' => $series, 'txn_date' => $date, 'voucher_status' => $status,
            'details' => 'phpunit fixture', 'office_id' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        foreach ($lines as $line) {
            DB::table('fin_general_ledger_details')->insert([
                'fin_gen_id' => $id, 'coa_id' => $line[0], 'detail' => 'line', 'debit' => $line[1], 'credit' => $line[2],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        return $id;
    }

    /* ---------------------------------------------------------------
     | Authentication & authorisation
     * --------------------------------------------------------------- */

    public function test_missing_key_is_rejected()
    {
        $this->getJson(self::BASE . '/')
            ->assertStatus(401)
            ->assertHeader('WWW-Authenticate')
            ->assertJsonPath('error.code', 'unauthenticated');
    }

    public function test_invalid_key_is_rejected()
    {
        $this->api('/', 'fms_' . $this->client->key_prefix . '_wrongsecret')->assertStatus(401);
        $this->api('/', 'not-a-key')->assertStatus(401);
    }

    public function test_bearer_and_x_api_key_headers_are_accepted()
    {
        $this->api('/')->assertOk()->assertJsonPath('data.client.name', 'phpunit');
        $this->withHeaders(['X-Api-Key' => $this->key])->getJson(self::BASE . '/voucher-types')->assertOk();
    }

    public function test_revoked_and_expired_keys_are_rejected_with_the_same_response()
    {
        $this->client->revoke();
        $revoked = $this->api('/')->assertStatus(401)->json('error.code');

        list($expired, $expiredKey) = ApiClient::issue('expired', ['gl.read']);
        $expired->forceFill(['expires_at' => now()->subMinute()])->save();
        $this->api('/', $expiredKey)->assertStatus(401)->assertJsonPath('error.code', $revoked);
    }

    public function test_key_without_scope_is_forbidden()
    {
        list(, $key) = ApiClient::issue('no scope', ['other.read']);

        $this->api('/', $key)->assertStatus(403)->assertJsonPath('error.code', 'insufficient_scope');
    }

    public function test_ip_allow_list_supports_cidr()
    {
        list(, $denied) = ApiClient::issue('office only', ['gl.read'], ['10.0.0.0/8']);
        list(, $allowed) = ApiClient::issue('localhost', ['gl.read'], ['127.0.0.0/8']);

        $this->api('/', $denied)->assertStatus(403)->assertJsonPath('error.code', 'ip_not_allowed');
        $this->api('/', $allowed)->assertOk();
    }

    public function test_query_string_key_only_works_when_enabled()
    {
        $this->getJson(self::BASE . '/?api_key=' . $this->key)->assertStatus(401);

        config(['api.auth.allow_query_key' => true]);
        $response = $this->getJson(self::BASE . '/accounts?limit=1&api_key=' . $this->key)->assertOk();

        $this->assertStringNotContainsString($this->key, $response->getContent(), 'Key must not be echoed in links.');
    }

    public function test_repeated_failures_are_throttled()
    {
        config(['api.auth.max_failed_attempts' => 3]);

        for ($i = 0; $i < 3; $i++) {
            $this->api('/', 'fms_x_y')->assertStatus(401);
        }
        $this->api('/')->assertStatus(429)->assertHeader('Retry-After');
    }

    public function test_keys_are_stored_hashed()
    {
        $row = (array) DB::table('api_clients')->where('id', $this->client->id)->first();

        $this->assertNotContains($this->key, $row);
        $this->assertSame(hash('sha256', $this->key), $row['key_hash']);
    }

    public function test_plain_http_is_rejected_in_production()
    {
        $this->app->detectEnvironment(function () {
            return 'production';
        });

        $this->api('/')->assertStatus(403)->assertJsonPath('error.code', 'https_required');
    }

    /* ---------------------------------------------------------------
     | Transport & error format
     * --------------------------------------------------------------- */

    public function test_security_headers_are_sent()
    {
        $this->api('/voucher-types')
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Request-Id')
            ->assertHeader('X-RateLimit-Limit');
    }

    public function test_api_is_read_only()
    {
        $this->withHeaders(['Authorization' => 'Bearer ' . $this->key])
            ->postJson(self::BASE . '/entries')
            ->assertStatus(405)
            ->assertJsonPath('error.code', 'method_not_allowed');
    }

    public function test_invalid_parameters_return_422()
    {
        $this->api('/entries?foo=1')->assertStatus(422)->assertJsonValidationErrors('foo', 'error.details');
        $this->api('/entries?date_from=2024-02-01&date_to=2024-01-01')->assertStatus(422)->assertJsonValidationErrors('date_to', 'error.details');
        $this->api('/entries?limit=999999')->assertStatus(422);
        $this->api('/entries?cursor=%%%')->assertStatus(422);
        $this->api("/entries?account_code=01'--")->assertStatus(422);
        $this->api('/reports/trial-balance')->assertStatus(422)->assertJsonValidationErrors(['date_from', 'date_to'], 'error.details');
        $this->api('/reports/trial-balance?date_from=1990-01-01&date_to=2099-12-31')->assertStatus(422);
    }

    public function test_updated_since_accepts_unencoded_plus_in_offset()
    {
        // "+05:00" sent without URL-encoding arrives as " 05:00".
        $this->api('/entries?limit=1&updated_since=2026-10-01T00:00:00+05:00')
            ->assertOk()
            ->assertJsonPath('meta.filters.updated_since', '2026-10-01T00:00:00+05:00');
        $this->api('/entries?limit=1&updated_since=2026-09-30T19:00:00Z')->assertOk();
    }

    public function test_unknown_resources_return_404_json()
    {
        $this->api('/vouchers/999999999')->assertStatus(404)->assertJsonPath('error.code', 'not_found');
        $this->api('/accounts/999999999')->assertStatus(404);
    }

    /* ---------------------------------------------------------------
     | Data
     * --------------------------------------------------------------- */

    public function test_entries_only_include_posted_vouchers_by_default()
    {
        list($a, , $posted, $prepared) = $this->seedLedger();

        $rows = $this->api('/entries?date_from=2099-01-01&date_to=2099-01-31')->assertOk()->json('data');
        $this->assertCount(2, $rows);
        $this->assertSame([$posted], array_values(array_unique(array_column($rows, 'voucher_id'))));

        $line = collect($rows)->firstWhere('account_id', $a);
        $this->assertSame('JV9001', $line['voucher_no']);
        $this->assertEquals(100.5, $line['debit']);
        $this->assertEquals(100.5, $line['net_amount']);

        $all = $this->api('/entries?date_from=2099-01-01&date_to=2099-01-31&status=all')->json('data');
        $this->assertCount(4, $all);
        $this->assertContains($prepared, array_column($all, 'voucher_id'));
    }

    public function test_cursor_pagination_walks_every_row_once()
    {
        $this->seedLedger();

        $seen = [];
        $url = self::BASE . '/entries?date_from=2099-01-01&date_to=2099-01-31&status=all&limit=3';
        $pages = 0;
        while ($url) {
            $json = $this->withHeaders(['Authorization' => 'Bearer ' . $this->key])->getJson($url)->assertOk()->json();
            foreach ($json['data'] as $row) {
                $seen[] = $row['entry_id'];
            }
            $url = $json['links']['next'];
            $pages++;
        }

        $this->assertSame(2, $pages);
        $this->assertCount(4, $seen);
        $this->assertSame($seen, array_values(array_unique($seen)));
    }

    public function test_voucher_detail_includes_lines_and_balance_flag()
    {
        list(, , $posted) = $this->seedLedger();

        $this->api('/vouchers/' . $posted)
            ->assertOk()
            ->assertJsonPath('data.voucher_no', 'JV9001')
            ->assertJsonPath('data.is_balanced', true)
            ->assertJsonPath('data.is_posted', true)
            ->assertJsonCount(2, 'data.lines');
    }

    public function test_trial_balance_and_ledger_agree()
    {
        list($a) = $this->seedLedger();

        $tb = $this->api('/reports/trial-balance?date_from=2099-01-01&date_to=2099-01-31')->assertOk()->json();
        $this->assertEquals(100.5, $tb['meta']['totals']['period_debit']);
        $this->assertEquals(100.5, $tb['meta']['totals']['period_credit']);

        $row = collect($tb['data'])->firstWhere('account_id', $a);
        $ledger = $this->api('/accounts/' . $a . '/ledger?date_from=2099-01-01&date_to=2099-01-31')->assertOk()->json('data');

        $this->assertCount(1, $ledger['lines']);
        $this->assertEquals($row['opening_balance'], $ledger['opening_balance']);
        $this->assertEquals($row['closing_balance'], $ledger['closing_balance']);
        $this->assertEquals(round($ledger['opening_balance'] + 100.5, 2), $ledger['lines'][0]['running_balance']);
    }

    public function test_accounts_are_flattened_with_hierarchy()
    {
        $row = $this->api('/accounts?level=L5&limit=1')->assertOk()->json('data.0');

        $this->assertSame('L5', $row['level']);
        $this->assertTrue($row['is_posting_account']);
        $this->assertSame($row['code'], $row['l5_code']);
        $this->assertStringStartsWith($row['l1_code'], $row['code']);
        $this->assertContains($row['normal_balance'], ['debit', 'credit']);
    }
}
