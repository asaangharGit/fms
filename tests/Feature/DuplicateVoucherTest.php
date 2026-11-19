<?php

namespace Tests\Feature;

use App\Models\FinGeneralLedger;
use App\Models\FinGeneralLedgerDetail;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * "Duplicate" on the Edit Voucher page (FinGeneralLedgerController@duplicate).
 */
class DuplicateVoucherTest extends TestCase
{
    use DatabaseTransactions;

    /** @var User */
    protected $admin;

    /** @var FinGeneralLedger */
    protected $voucher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->makeUser('super_admin', '13,14,21,22', 'phpunit-dup-admin@example.com');

        $accounts = DB::table('fin_chart_of_accounts')->where('level', 'L5')->limit(2)->pluck('id');
        $this->voucher = FinGeneralLedger::create([
            'user_id' => $this->admin->id, 'details' => 'phpunit duplicate source', 'debit' => 1500.50, 'credit' => 1500.50,
            'txn_date' => '2026-09-30', 'txn_type' => 5, 'txn_series' => $this->nextSeries(), 'office_id' => 1,
            'vendor_id' => 7, 'invoices_id' => 99, 'cheque_id' => 3, 'cheque_number' => '123456', 'voucher_status' => 3,
        ]);
        foreach ([[$accounts[0], 1500.50, 0], [$accounts[1], 0, 1500.50]] as $line) {
            FinGeneralLedgerDetail::create([
                'fin_gen_id' => $this->voucher->id, 'coa_id' => $line[0], 'detail' => 'phpunit line', 'debit' => $line[1], 'credit' => $line[2],
            ]);
        }
    }

    protected function makeUser($type, $subMenus, $email)
    {
        $roleId = DB::table('roles')->insertGetId([
            'role_name' => 'phpunit ' . $email, 'main_menu_ids' => '5', 'sub_menu_ids' => $subMenus,
            'crud_rights' => '', 'hide_confidentiality' => 'no', 'username' => 'phpunit', 'status' => 1,
            'created_at' => now()->toDateString(), 'updated_at' => now()->toDateString(),
        ]);

        $user = new User();
        $user->forceFill([
            'name' => 'phpunit ' . $type, 'email' => $email, 'password' => Hash::make('Original-pass-1'),
            'role_id' => $roleId, 'user_type' => $type,
        ])->save();

        return $user;
    }

    protected function nextSeries()
    {
        return (int) FinGeneralLedger::where('txn_type', 5)->max('txn_series') + 1;
    }

    protected function duplicate($user, array $data)
    {
        $_SERVER['REQUEST_URI'] = '/finance/ledger/edit/' . $this->voucher->id;

        return $this->actingAs($user)->withSession(['ip_address' => '127.0.0.1'])
            ->from('/finance/ledger/edit/' . $this->voucher->id)
            ->post('/finance/ledger/duplicate/' . $this->voucher->id, $data);
    }

    public function test_creates_the_requested_copies_with_consecutive_numbers()
    {
        $first = $this->nextSeries();

        $this->duplicate($this->admin, ['copies' => 3, 'date_mode' => 'original'])
            ->assertRedirect(route('fin-general-ledgers.vouchers'))
            ->assertSessionHas('flash_success', sprintf('JV%d duplicated: 3 vouchers (JV%d to JV%d) created as Prepared.', $this->voucher->txn_series, $first, $first + 2));

        $copies = FinGeneralLedger::where('txn_type', 5)->where('txn_series', '>=', $first)->orderBy('txn_series')->get();

        $this->assertSame([$first, $first + 1, $first + 2], $copies->pluck('txn_series')->all());
        foreach ($copies as $copy) {
            $this->assertSame('phpunit duplicate source', $copy->details);
            $this->assertSame('2026-09-30', $copy->txn_date);
            $this->assertSame(1, (int) $copy->voucher_status);
            $this->assertSame($this->admin->id, (int) $copy->user_id);
            $this->assertSame(7, (int) $copy->vendor_id);
            $this->assertNull($copy->invoices_id);
            $this->assertNull($copy->cheque_id);
            $this->assertNull($copy->cheque_number);
            $this->assertEquals(1500.50, $copy->debit);
            $this->assertEquals(1500.50, $copy->credit);

            $lines = FinGeneralLedgerDetail::where('fin_gen_id', $copy->id)->orderBy('id')->get(['coa_id', 'detail', 'debit', 'credit'])->toArray();
            $original = FinGeneralLedgerDetail::where('fin_gen_id', $this->voucher->id)->orderBy('id')->get(['coa_id', 'detail', 'debit', 'credit'])->toArray();
            $this->assertSame($original, $lines);
        }

        $this->assertSame(3, (int) $this->voucher->fresh()->voucher_status, 'original is untouched');
    }

    public function test_copies_can_use_a_different_date()
    {
        $first = $this->nextSeries();

        $this->duplicate($this->admin, ['copies' => 1, 'date_mode' => 'custom', 'txn_date' => '2026-10-31'])
            ->assertSessionHas('flash_success', sprintf('JV%d duplicated: JV%d created as Prepared.', $this->voucher->txn_series, $first));

        $this->assertSame('2026-10-31', FinGeneralLedger::where('txn_type', 5)->where('txn_series', $first)->value('txn_date'));
    }

    public function test_invalid_input_creates_nothing()
    {
        $before = FinGeneralLedger::count();

        foreach ([
            ['copies' => 0, 'date_mode' => 'original'],
            ['copies' => 51, 'date_mode' => 'original'],
            ['copies' => 'abc', 'date_mode' => 'original'],
            ['copies' => 2, 'date_mode' => 'custom'],
            ['copies' => 2, 'date_mode' => 'custom', 'txn_date' => '31/10/2026'],
        ] as $data) {
            $this->duplicate($this->admin, $data)
                ->assertRedirect('/finance/ledger/edit/' . $this->voucher->id)
                ->assertSessionHasErrors(null, null, 'duplicate');
        }

        $this->assertSame($before, FinGeneralLedger::count());
    }

    public function test_user_without_ledger_permission_is_blocked()
    {
        $user = $this->makeUser('user', '', 'phpunit-dup-user@example.com');
        $before = FinGeneralLedger::count();

        $this->duplicate($user, ['copies' => 1, 'date_mode' => 'original'])->assertForbidden();

        $this->assertSame($before, FinGeneralLedger::count());
    }

    public function test_edit_page_shows_the_duplicate_button_and_dialog()
    {
        $_SERVER['REQUEST_URI'] = '/finance/ledger/edit/' . $this->voucher->id;

        $this->actingAs($this->admin)->withSession(['ip_address' => '127.0.0.1'])
            ->get('/finance/ledger/edit/' . $this->voucher->id)
            ->assertOk()
            ->assertSee('data-target="#duplicateVoucherModal"', false)
            ->assertSee('action="' . route('fin-general-ledgers.duplicate', $this->voucher->id) . '"', false)
            ->assertSee('var nextSeries = ' . $this->nextSeries() . ';', false);
    }
}
