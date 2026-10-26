<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Server-side permissions (CheckPagePermission), user management fixes,
 * attachment whitelist and web security headers.
 */
class AccessControlTest extends TestCase
{
    use DatabaseTransactions;

    /** @var User */
    protected $superAdmin;

    /** @var User */
    protected $ledgerUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Voucher pages only (finance/ledger), no chart of accounts, no admin pages.
        $roleId = DB::table('roles')->insertGetId([
            'role_name' => 'phpunit ledger only', 'main_menu_ids' => '5', 'sub_menu_ids' => '13,14,21,22',
            'crud_rights' => '', 'hide_confidentiality' => 'no', 'username' => 'phpunit', 'status' => 1,
            'created_at' => now()->toDateString(), 'updated_at' => now()->toDateString(),
        ]);

        $this->superAdmin = $this->makeUser('super_admin', $roleId, 'phpunit-admin@example.com');
        $this->ledgerUser = $this->makeUser('user', $roleId, 'phpunit-user@example.com');
    }

    protected function makeUser($type, $roleId, $email)
    {
        $user = new User();
        $user->forceFill([
            'name' => 'phpunit ' . $type, 'email' => $email, 'password' => Hash::make('Original-pass-1'),
            'role_id' => $roleId, 'user_type' => $type,
        ])->save();

        return $user;
    }

    /** Pages render the master layout, which needs these. */
    protected function page($user, $uri)
    {
        $_SERVER['REQUEST_URI'] = $uri;

        return $this->actingAs($user)->withSession(['ip_address' => '127.0.0.1']);
    }

    public function test_non_super_admin_cannot_change_a_super_admin_account()
    {
        $this->page($this->ledgerUser, '/updateUsers')
            ->post('/updateUsers/' . $this->superAdmin->id, [
                'name' => 'x', 'email' => 'taken@example.com', 'role_id' => $this->superAdmin->role_id, 'password' => 'Takeover-123',
            ])
            ->assertForbidden();

        $this->assertTrue(Hash::check('Original-pass-1', $this->superAdmin->fresh()->password));
    }

    public function test_blank_password_keeps_the_current_password()
    {
        $this->page($this->superAdmin, '/updateUsers')
            ->post('/updateUsers/' . $this->ledgerUser->id, [
                'name' => 'Renamed', 'email' => $this->ledgerUser->email, 'role_id' => $this->ledgerUser->role_id, 'password' => '',
            ])
            ->assertRedirect();

        $fresh = $this->ledgerUser->fresh();
        $this->assertSame('Renamed', $fresh->name);
        $this->assertTrue(Hash::check('Original-pass-1', $fresh->password));
    }

    public function test_user_edit_form_does_not_expose_the_password_hash()
    {
        $html = $this->page($this->superAdmin, '/editUsers/' . $this->ledgerUser->id)
            ->get('/editUsers/' . $this->ledgerUser->id)
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString($this->ledgerUser->password, $html);
        $this->assertStringContainsString('type="password"', $html);
    }

    public function test_module_without_permission_is_blocked_on_the_server()
    {
        $account = DB::table('fin_chart_of_accounts')->where('level', 'L5')->value('id');

        $this->page($this->ledgerUser, '/finance/accounts/destroy/' . $account)
            ->get('/finance/accounts/destroy/' . $account)
            ->assertForbidden();

        $this->assertTrue(DB::table('fin_chart_of_accounts')->where('id', $account)->exists());
    }

    public function test_permitted_module_still_works()
    {
        $this->page($this->ledgerUser, '/finance/ledger/vouchers')
            ->get('/finance/ledger/vouchers')
            ->assertOk()
            ->assertDontSee('Permission denied');
    }

    public function test_cross_module_lookup_endpoints_stay_available()
    {
        // The voucher form calls vendor lookups; this role has no vendor pages.
        $status = $this->page($this->ledgerUser, '/finance/vendors/getChequeNum')
            ->get('/finance/vendors/getChequeNum')
            ->getStatusCode();

        $this->assertNotSame(403, $status);
    }

    public function test_menu_management_is_super_admin_only()
    {
        $this->page($this->ledgerUser, '/storeMainMenu')
            ->post('/storeMainMenu', ['main_menu_name' => 'x', 'title_name' => 'phpunit menu', 'menu_type' => 1, 'icon' => 'x'])
            ->assertForbidden();

        $this->assertFalse(DB::table('main_menus')->where('title', 'phpunit menu')->exists());
    }

    public function test_cross_site_delete_link_is_blocked_even_for_super_admin()
    {
        $account = DB::table('fin_chart_of_accounts')->where('level', 'L5')->value('id');

        $this->page($this->superAdmin, '/finance/accounts/destroy/' . $account)
            ->withHeaders(['Sec-Fetch-Site' => 'cross-site'])
            ->get('/finance/accounts/destroy/' . $account)
            ->assertForbidden();
        $this->assertTrue(DB::table('fin_chart_of_accounts')->where('id', $account)->exists());

        $this->page($this->superAdmin, '/finance/accounts/destroy/' . $account)
            ->withHeaders(['Sec-Fetch-Site' => 'same-origin'])
            ->get('/finance/accounts/destroy/' . $account)
            ->assertRedirect();
        $this->assertFalse(DB::table('fin_chart_of_accounts')->where('id', $account)->exists());
    }

    public function test_executable_attachments_are_rejected()
    {
        $before = DB::table('fin_general_ledgers')->count();

        $this->page($this->superAdmin, '/finance/ledger/store')
            ->post('/finance/ledger/store', ['filesupload' => [UploadedFile::fake()->create('shell.php', 1)]])
            ->assertSessionHasErrors('filesupload.0');

        $this->assertSame($before, DB::table('fin_general_ledgers')->count());
    }

    public function test_web_pages_send_security_headers()
    {
        $this->get('/login')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeaderMissing('X-Powered-By');
    }
}
