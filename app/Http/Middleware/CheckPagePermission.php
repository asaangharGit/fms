<?php

namespace App\Http\Middleware;

use App\Models\SubMenu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Server-side enforcement of the role permissions that the UI already uses.
 *
 * Before this, permissions were only checked while rendering the layout
 * ("Permission denied..." instead of the page content), so controller
 * actions - store/update/destroy, JSON endpoints - ran for any logged-in
 * user. This middleware applies the same role data on the server:
 *
 *  - super_admin: everything.
 *  - Main/Sub menu management: super_admin only (the menu shows it only to them).
 *  - Users / Roles / Activity pages: role must have one of their sub menus.
 *  - Other modules (finance/ledger, finance/accounts, reports, ...): role must
 *    have at least one sub menu of that module. This is deliberately looser
 *    than the page-level UI check, so nothing the UI allows is blocked.
 *  - Small read-only lookup endpoints used by forms of other modules are exempt.
 *
 * It also blocks cross-site requests to the GET routes that delete data.
 */
class CheckPagePermission
{
    /** Paths only a super_admin may use. */
    const SUPER_ADMIN_ONLY = ['addMainMenu', 'storeMainMenu', 'addSubMenu', 'storeSubMenu'];

    /** Explicit groups: first path segment => sub menu ids that grant access. */
    const GROUPS = [
        'addUsers' => [55, 56], 'viewUsers' => [55, 56], 'editUsers' => [55, 56],
        'updateUsers' => [55, 56], 'storeUsers' => [55, 56], 'getUsers' => [55, 56],
        'addRoles' => [57, 58], 'viewRoles' => [57, 58], 'storeRoles' => [57, 58], 'deleteRoles' => [57, 58],
        'loginActivity' => [87, 88], 'PageActivity' => [87, 88],
        'userPageActivity' => [87, 88], 'loginPageActivity' => [87, 88],
    ];

    /** Read-only helpers called from forms of other modules, plus per-user pages. */
    const EXEMPT = [
        '#^(dashboard|home|dashboardData|storeLogs|my_account(/.*)?|uploads/.*)?$#',
        '#^finance/vendors/(getChequeNum|getVendorInvoices|getInvoiceAmount/[^/]+|openVendorForm)$#',
        '#^finance/ledger/(chequeNumExist|addMoreRowVoucher|addMoreRowEditVoucher|chartOfAccountsJson)$#',
        '#^finance/accounts/getL4Code$#',
        '#^fixedassets/subitems/getItemCode$#',
    ];

    /** GET routes that delete data (CSRF-sensitive). */
    const DESTRUCTIVE_GET = '#(^|/)(destroy|deleteRoles|VendorInvoiceDestroy)(/|$)#i';

    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') && preg_match(self::DESTRUCTIVE_GET, $request->path()) && $this->isCrossSite($request)) {
            return $this->deny($request, 'Cross-site request blocked.');
        }

        $user = Auth::user();
        if ($user === null || $user->user_type === 'super_admin') {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        $first = explode('/', $path)[0];

        if (in_array($first, self::SUPER_ADMIN_ONLY, true)) {
            return $this->deny($request);
        }

        foreach (self::EXEMPT as $pattern) {
            if (preg_match($pattern, $path)) {
                return $next($request);
            }
        }

        $required = isset(self::GROUPS[$first]) ? self::GROUPS[$first] : $this->moduleSubMenus($path);
        if ($required === null) {
            return $next($request); // no menu configured for this module: unchanged behaviour
        }

        $granted = array_map('intval', array_filter(explode(',', (string) DB::table('roles')->where('id', $user->role_id)->value('sub_menu_ids'))));

        return array_intersect($required, $granted) ? $next($request) : $this->deny($request);
    }

    /**
     * Sub menu ids belonging to the request's module, or null if none exist.
     */
    protected function moduleSubMenus($path)
    {
        $segments = explode('/', $path);
        $module = in_array($segments[0], ['finance', 'fixedassets'], true) && isset($segments[1])
            ? $segments[0] . '/' . $segments[1]
            : $segments[0];

        $ids = SubMenu::where('status', 1)
            ->where(function ($q) use ($module) {
                $q->where('m_controller_name', $module)->orWhere('m_controller_name', 'like', $module . '/%');
            })
            ->pluck('id')
            ->map(function ($id) { return (int) $id; })
            ->all();

        return $ids ?: null;
    }

    protected function isCrossSite(Request $request)
    {
        $site = $request->headers->get('Sec-Fetch-Site');
        if ($site !== null) {
            return ! in_array($site, ['same-origin', 'none'], true);
        }

        $referer = $request->headers->get('Referer');

        return $referer !== null && parse_url($referer, PHP_URL_HOST) !== $request->getHost();
    }

    protected function deny(Request $request, $message = 'You do not have permission to perform this action.')
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 403);
        }

        abort(403, $message);
    }
}
