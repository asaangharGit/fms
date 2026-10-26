<?php

namespace App\Http\Controllers;

/**
 * Serves voucher attachments from base_path('uploads') to logged-in users.
 *
 * The document root is the project root, so these files used to be
 * downloadable by anyone who guessed /uploads/{voucher}/{file}. The
 * .htaccess now routes /uploads/* here instead, keeping the same URLs.
 */
class UploadController extends Controller
{
    /** Types safe to display in the browser; everything else is downloaded. */
    const INLINE_TYPES = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];

    public function show($voucher, $file)
    {
        $base = realpath(base_path('uploads'));
        $path = $base === false ? false : realpath($base . DIRECTORY_SEPARATOR . $voucher . DIRECTORY_SEPARATOR . $file);

        // realpath() resolves "..", so this also rejects path traversal.
        if ($path === false || strpos($path, $base . DIRECTORY_SEPARATOR) !== 0 || ! is_file($path)) {
            abort(404);
        }

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ];

        if (in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::INLINE_TYPES, true)) {
            return response()->file($path, $headers);
        }

        return response()->download($path, basename($path), $headers);
    }
}
