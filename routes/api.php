<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\Api\BorrowerapiController;
use App\Http\Controllers\Api\V1\GeneralLedger\AccountController as GlAccountController;
use App\Http\Controllers\Api\V1\GeneralLedger\EntryController as GlEntryController;
use App\Http\Controllers\Api\V1\GeneralLedger\MetaController as GlMetaController;
use App\Http\Controllers\Api\V1\GeneralLedger\ReferenceDataController as GlReferenceDataController;
use App\Http\Controllers\Api\V1\GeneralLedger\ReportController as GlReportController;
use App\Http\Controllers\Api\V1\GeneralLedger\VoucherController as GlVoucherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//Route::get('students', 'ApiController@getAllStudents');
//Route::get('students/{id}', 'ApiController@getStudent');
//Route::get('borrower', 'BorrowerapiController@addBorrower');
//Route::put('students/{id}', 'ApiController@updateStudent');
//Route::delete('students/{id}','ApiController@deleteStudent');
//Route::post("borrower",[BorrowerapiController::class, "addBorrower"]);
//Route::get("borrower",[BorrowerapiController::class, "addBorrower"]);
//
//Route::group([
//    'middleware' => 'api',
//    'prefix' => 'auth'
//
//], function ($router) {
//    Route::post('/login', [AuthController::class, 'login']);
//    Route::post('/register', [AuthController::class, 'register']);
//    Route::post('/logout', [AuthController::class, 'logout']);
//    Route::post('/refresh', [AuthController::class, 'refresh']);
//    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
//});
//Route::prefix("v1")->group(function(){
//    Route::post('/login',[AuthController::class,'login']);
//});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
});
Route::middleware('auth:api')->group( function () {
    //Route::get('/products', [ProductController::class,'index']);
    //Route::resource('products', ProductController::class);
    Route::post("borrower",[BorrowerapiController::class, "addBorrower"]);
});

/*
|--------------------------------------------------------------------------
| General Ledger API v1 (read-only, Power BI)
|--------------------------------------------------------------------------
|
| Auth: API key (php artisan api-client:create) with scope "gl.read".
| Middleware order matters: transport hardening -> key auth -> per-client
| rate limit. The shared "throttle:api" limiter is replaced by the
| per-client one. See docs/api/general-ledger/README.md.
|
*/
Route::group([
    'prefix' => 'v1/gl',
    'as' => 'api.v1.gl.',
    'middleware' => ['api.secure', 'api.client:gl.read', 'throttle:api-client'],
    'excluded_middleware' => ['throttle:api'],
], function () {
    Route::get('/', [GlMetaController::class, 'show'])->name('meta');

    Route::get('voucher-types', [GlReferenceDataController::class, 'voucherTypes'])->name('voucher-types');
    Route::get('voucher-statuses', [GlReferenceDataController::class, 'voucherStatuses'])->name('voucher-statuses');
    Route::get('account-classes', [GlReferenceDataController::class, 'accountClasses'])->name('account-classes');

    Route::get('accounts', [GlAccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/{account}', [GlAccountController::class, 'show'])->whereNumber('account')->name('accounts.show');
    Route::get('accounts/{account}/ledger', [GlReportController::class, 'accountLedger'])->whereNumber('account')->name('accounts.ledger');

    Route::get('vouchers', [GlVoucherController::class, 'index'])->name('vouchers.index');
    Route::get('vouchers/{voucher}', [GlVoucherController::class, 'show'])->whereNumber('voucher')->name('vouchers.show');

    Route::get('entries', [GlEntryController::class, 'index'])->name('entries.index');

    Route::get('reports/trial-balance', [GlReportController::class, 'trialBalance'])->name('reports.trial-balance');
    Route::get('reports/monthly-balances', [GlReportController::class, 'monthlyBalances'])->name('reports.monthly-balances');
});