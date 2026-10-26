<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Voucher attachments under /uploads must only be served to logged-in
 * users (see UploadController and .htaccess).
 */
class UploadsTest extends TestCase
{
    use DatabaseTransactions;

    const VOUCHER = '999999999';

    /** @var string */
    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = base_path('uploads/' . self::VOUCHER);
        @mkdir($this->dir, 0755, true);
        file_put_contents($this->dir . '/receipt.pdf', '%PDF-1.4 phpunit');
        file_put_contents($this->dir . '/page.html', '<script>alert(1)</script>');
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') as $file) {
            unlink($file);
        }
        @rmdir($this->dir);

        parent::tearDown();
    }

    protected function user()
    {
        return User::query()->firstOrFail();
    }

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/uploads/' . self::VOUCHER . '/receipt.pdf')->assertRedirect('/login');
    }

    public function test_logged_in_user_gets_the_file_inline()
    {
        $response = $this->actingAs($this->user())->get('/uploads/' . self::VOUCHER . '/receipt.pdf');

        $response->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertSame('%PDF-1.4 phpunit', file_get_contents($response->baseResponse->getFile()->getPathname()));
    }

    public function test_active_content_is_downloaded_not_rendered()
    {
        $this->actingAs($this->user())
            ->get('/uploads/' . self::VOUCHER . '/page.html')
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=page.html');
    }

    public function test_path_traversal_and_missing_files_return_404()
    {
        $this->actingAs($this->user());

        $this->get('/uploads/' . self::VOUCHER . '/..%2F..%2F.env')->assertNotFound();
        $this->get('/uploads/' . self::VOUCHER . '/missing.pdf')->assertNotFound();
        $this->get('/uploads/abc/receipt.pdf')->assertNotFound();
    }
}
