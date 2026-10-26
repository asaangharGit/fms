<?php

Auth::routes(['register' => false]);
Route::get( '/logout', function() {

    Auth::logout();
    return view('auth.login');
});

//Route::get('/test', 'TestController@index')->name('test');
//Route::get('/privacy-policy', 'HomeController@privacy_policy')->name('privacy_policy');
//Route::get('/terms-of-use', 'HomeController@terms_of_use')->name('terms_of_use');
//Route::post('/savefeesdata', 'LoansController@FeesCollection');
//Route::get('/generatePayslip', 'UserController@generatePayslip');

Route::group(['middleware' => ['auth', 'page.permission']], function () {

    // Voucher attachments (login required; see UploadController)
    Route::get('/uploads/{voucher}/{file}', 'UploadController@show')
        ->where(['voucher' => '[0-9]+', 'file' => '[^/]+'])
        ->name('uploads.show');

    Route::get('/dashboardData', 'HomeController@dashboardData')->name('dashboardData');
    Route::get('/', 'HomeController@dashboard')->name('home');
    Route::get('/home', 'HomeController@dashboard')->name('home');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

    Route::group(['prefix' => 'my_account'], function () {
        Route::get('/', 'MyAccountController@edit_profile')->name('my_account');
        Route::put('/', 'MyAccountController@update_profile')->name('my_account.update');
        Route::put('/change_password', 'MyAccountController@change_pass')->name('my_account.change_pass');
    });
    /* Administrations */
    Route::group(['prefix' => 'general'], function () {
//        Route::group(['prefix' => 'office'], function () {
//            Route::get('/', 'GeneralOfficeController@index');
//            Route::get('index', 'GeneralOfficeController@index')->name('general-offices.index');
//            Route::get('create', 'GeneralOfficeController@create')->name('general-offices.create');
//            Route::get('destroy/{ttr}', 'GeneralOfficeController@destroy')->name('general-offices.destroy');
//            Route::get('show/{ttr}', 'GeneralOfficeController@show')->name('general-offices.show');
//            Route::get('edit/{ttr}', 'GeneralOfficeController@edit')->name('general-offices.edit');
//            Route::patch('update/{ttr}', 'GeneralOfficeController@update')->name('general-offices.update');
//            Route::post('store', 'GeneralOfficeController@store')->name('general-offices.store');
//        });
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'GeneralUserController@index');
            Route::get('index', 'GeneralUserController@index')->name('general-users.index');
            Route::get('create', 'GeneralUserController@create')->name('general-users.create');
            Route::get('destroy/{ttr}', 'GeneralUserController@destroy')->name('general-users.destroy');
            Route::get('show/{ttr}', 'GeneralUserController@show')->name('users.show');
            Route::get('show/{ttr}', 'GeneralUserController@show')->name('general-users.show');
            Route::get('edit/{ttr}', 'GeneralUserController@edit')->name('general-users.edit');
            Route::patch('update/{ttr}', 'GeneralUserController@update')->name('general-users.update');
            Route::post('store', 'GeneralUserController@store')->name('general-users.store');
        });

//
//        Route::group(['prefix' => 'valuations'], function () {
//            Route::get('/', 'CompanyValuationController@index')->name('valuations');
//            Route::get('index', 'CompanyValuationController@index')->name('company-valuations.index');
//            Route::get('create', 'CompanyValuationController@create')->name('company-valuations.create');
//            Route::get('destroy/{ttr}', 'CompanyValuationController@destroy')->name('company-valuations.destroy');
//            Route::get('/show/{ttr}', 'CompanyValuationController@show')->name('company-valuations.show');
//            Route::get('edit/{ttr}', 'CompanyValuationController@edit')->name('company-valuations.edit');
//            Route::patch('update/{ttr}', 'CompanyValuationController@update')->name('company-valuations.update');
//            Route::post('store', 'CompanyValuationController@store')->name('company-valuations.store');
//        });
//        Route::group(['prefix' => 'legal'], function () {
//            Route::get('/', 'CompanyLegalController@index')->name('legal');
//            Route::get('index', 'CompanyLegalController@index')->name('company-legals.index');
//            Route::get('create', 'CompanyLegalController@create')->name('company-legals.create');
//
//            Route::get('destroy/{ttr}', 'CompanyLegalController@destroy')->name('company-legals.destroy');
//            Route::get('/show/{ttr}', 'CompanyLegalController@show')->name('company-legals.show');
//            Route::get('edit/{ttr}', 'CompanyLegalController@edit')->name('company-legals.edit');
//            Route::patch('update/{ttr}', 'CompanyLegalController@update')->name('company-legals.update');
//            Route::post('store', 'CompanyLegalController@store')->name('company-legals.store');
//        });
//        Route::group(['prefix' => 'incomeest'], function () {
//            Route::get('/', 'CompanyIncomeEstController@index')->name('company-income-ests.index');
//            Route::get('index', 'CompanyIncomeEstController@index')->name('company-income-ests.index');
//            Route::get('create', 'CompanyIncomeEstController@create')->name('company-income-ests.create');
//
//            Route::get('destroy/{ttr}', 'CompanyIncomeEstController@destroy')->name('company-income-ests.destroy');
//            Route::get('/show/{ttr}', 'CompanyIncomeEstController@show')->name('company-income-ests.show');
//            Route::get('edit/{ttr}', 'CompanyIncomeEstController@edit')->name('company-income-ests.edit');
//            Route::patch('update/{ttr}', 'CompanyIncomeEstController@update')->name('company-income-ests.update');
//            Route::post('store', 'CompanyIncomeEstController@store')->name('company-income-ests.store');
//        });


    });
    /*     * ************* Reports **************** */

    /*     * ************* Reports **************** */
    Route::group(['prefix' => 'reports'], function () {

        Route::get('generalLegderReportForm', 'ReportsController@financial')->name('reports.financial');
        Route::post('generalLegderReportDetail', 'ReportsController@generalLegderReportDetail')->name('reports.generalLegderReportDetail');

        Route::get('trial', 'ReportsController@trialbalance')->name('reports.trial');
        Route::post('trialreport', 'ReportsController@trialbalancereport')->name('reports.trialreport');

        Route::get('trialSumReportForm', 'ReportsController@trialSumReportForm')->name('reports.trialSumReportForm');
        Route::post('trialSumReportDetail', 'ReportsController@trialSumReportDetail')->name('reports.trialSumReportDetail');


        Route::get('profitLossReportForm', 'ReportsController@profitLossReportForm')->name('reports.profitLossReportForm');
        Route::post('profitLossReportDetail', 'ReportsController@profitLossReportDetail')->name('reports.profitLossReportDetail');

        Route::get('BalancetSheetReportForm', 'ReportsController@BalancetSheetReportForm')->name('reports.BalancetSheetReportForm');
        Route::post('BalancetSheetReportDetail', 'ReportsController@BalancetSheetReportDetail')->name('reports.BalancetSheetReportDetail');


        Route::get('assetDepreciationReportForm', 'ReportsController@assetDepreciationReportForm')->name('reports.assetDepreciationReportForm');
        Route::post('assetDepreciationReportDetail', 'ReportsController@assetDepreciationReportDetail')->name('reports.assetDepreciationReportDetail');


        Route::get('leaseObligationForm', 'ReportsController@leaseObligationForm')->name('reports.leaseObligationForm');
        Route::post('leaseObligationDetail', 'ReportsController@leaseObligationDetail')->name('reports.leaseObligationDetail');

        Route::get('vendorLedgerReportForm', 'ReportsController@vendorLedgerReportForm')->name('reports.vendorLedgerReportForm');
        Route::post('vendorLedgerReportDetail', 'ReportsController@vendorLedgerReportDetail')->name('reports.vendorLedgerReportDetail');

        Route::get('receivableReportForm', 'ReportsController@receivableReportForm')->name('reports.receivableReportForm');
        Route::post('receivableReportDetail', 'ReportsController@receivableReportDetail')->name('reports.receivableReportDetail');



    });

    Route::group(['prefix' => 'finance'], function () {
        Route::group(['prefix' => 'ledger'], function () {
            Route::get('/', 'FinGeneralLedgerController@index');
            Route::get('/', 'FinGeneralLedgerController@index')->name('fin-general-ledgers.index');
            Route::get('create', 'FinGeneralLedgerController@create')->name('fin-general-ledgers.create');
            Route::get('filemanager', 'FinGeneralLedgerController@filemanager')->name('fin-general-ledgers.filemanager');
            Route::post('upload', 'FinGeneralLedgerController@upload')->name('fin-general-ledgers.upload');
            Route::get('destroy/{ttr}', 'FinGeneralLedgerController@destroy')->name('fin-general-ledgers.destroy');
            Route::get('/show/{ttr}', 'FinGeneralLedgerController@show')->name('fin-general-ledgers.show');
            Route::get('/show2/{ttr}', 'FinGeneralLedgerController@show2')->name('fin-general-ledgers.show2');
            Route::get('/process/{ttr}', 'FinGeneralLedgerController@process')->name('fin-general-ledgers.process');
            Route::get('/vouchers', 'VoucherController@index')->name('fin-general-ledgers.vouchers');
            Route::post('/searchvouchers', 'VoucherController@searchvouchers')->name('fin-general-ledgers.searchvouchers');

            Route::get('edit/{ttr}', 'FinGeneralLedgerController@edit')->name('fin-general-ledgers.edit');
            Route::patch('update/{ttr}', 'FinGeneralLedgerController@update')->name('fin-general-ledgers.update');
            Route::post('store', 'FinGeneralLedgerController@store')->name('fin-general-ledgers.store');
            Route::post('clone', 'FinGeneralLedgerController@clone')->name('fin-general-ledgers.clone');
            Route::post('/submitvouchers', 'FinGeneralLedgerController@submitvouchers');
            Route::post('/bulkApproveLedger', 'VoucherController@bulkApproveLedger')->name('fin-general-ledgers.bulkApprove');
            Route::get('/addMoreRowVoucher', 'VoucherController@addMoreRowVoucher')->name('fin-general-ledgers.addMoreRowVoucher');
            Route::get('/addMoreRowEditVoucher', 'VoucherController@addMoreRowEditVoucher')->name('fin-general-ledgers.addMoreRowEditVoucher');
            Route::get('/chartOfAccountsJson', 'VoucherController@chartOfAccountsJson');
            Route::get('/cloneVoucher/{id}', 'FinGeneralLedgerController@cloneVoucher')->name("fin-general-ledgers.cloneVoucher");
            Route::get('/bulkprintForm', 'FinGeneralLedgerController@bulkprintForm')->name("fin-general-ledgers.bulkprintForm");
            Route::post('/bulkprintAll', 'FinGeneralLedgerController@bulkprintAll')->name("fin-general-ledgers.bulkprintAll");
            Route::get('/chequeNumExist', 'FinGeneralLedgerController@chequeNumExist')->name('fin-general-ledgers.chequeNumExist');




        });
        Route::group(['prefix' => 'checkbook'], function () {
            Route::get('/', 'FinCheckbookController@index');
            Route::get('index', 'FinCheckbookController@index')->name('fin-checkbooks.index');
            Route::get('create', 'FinCheckbookController@create')->name('fin-checkbooks.create');
            Route::get('destroy/{ttr}', 'FinCheckbookController@destroy')->name('fin-checkbooks.destroy');
            Route::get('show/{ttr}', 'FinCheckbookController@show')->name('fin-checkbooks.show');
            Route::get('edit/{ttr}', 'FinCheckbookController@edit')->name('fin-checkbooks.edit');
            Route::patch('update/{ttr}', 'FinCheckbookController@update')->name('fin-checkbooks.update');
            Route::post('store', 'FinCheckbookController@store')->name('fin-checkbooks.store');
        });
        Route::group(['prefix' => 'accounts'], function () {
            Route::get('/', 'FinChartOfAccountController@index');
            Route::get('index', 'FinChartOfAccountController@index')->name('fin-chart-of-accounts.index');
            Route::get('create', 'FinChartOfAccountController@create')->name('fin-chart-of-accounts.create');
            Route::get('destroy/{ttr}', 'FinChartOfAccountController@destroy')->name('fin-chart-of-accounts.destroy');
            Route::get('show/{ttr}', 'FinChartOfAccountController@show')->name('fin-chart-of-accounts.show');
            Route::get('edit/{ttr}', 'FinChartOfAccountController@edit')->name('fin-chart-of-accounts.edit');
            Route::patch('update/{ttr}', 'FinChartOfAccountController@update')->name('fin-chart-of-accounts.update');
            Route::post('store', 'FinChartOfAccountController@store')->name('fin-chart-of-accounts.store');
        });
        Route::group(['prefix' => 'banks'], function () {
            Route::get('/', 'FinBanksAccountController@index');
            Route::get('index', 'FinBanksAccountController@index')->name('fin-banks-accounts.index');
            Route::get('create', 'FinBanksAccountController@create')->name('fin-banks-accounts.create');
            Route::get('destroy/{ttr}', 'FinBanksAccountController@destroy')->name('fin-banks-accounts.destroy');
            Route::get('show/{trr}', 'FinBanksAccountController@show')->name('fin-banks-accounts.show');
            Route::get('edit/{ttr}', 'FinBanksAccountController@edit')->name('fin-banks-accounts.edit');
            Route::patch('update/{ttr}', 'FinBanksAccountController@update')->name('fin-banks-accounts.update');
            Route::post('store', 'FinBanksAccountController@store')->name('fin-banks-accounts.store');
        });
        Route::group(['prefix' => 'vendors'], function () {

            Route::get('/', 'FinVendorsController@index');
            Route::get('index', 'FinVendorsController@index')->name('fin-vendors.index');
            Route::get('create', 'FinVendorsController@create')->name('fin-vendors.create');
            Route::get('destroy/{ttr}', 'FinVendorsController@destroy')->name('fin-vendors.destroy');
            Route::get('show/{trr}', 'FinVendorsController@show')->name('fin-vendors.show');
            Route::get('edit/{ttr}', 'FinVendorsController@edit')->name('fin-vendors.edit');
            Route::patch('update/{ttr}', 'FinVendorsController@update')->name('fin-vendors.update');
            Route::post('store', 'FinVendorsController@store')->name('fin-vendors.store');
            Route::post('storeRecievedPayment', 'FinVendorsController@storeRecievedPayment')->name('fin-vendors.storeRecievedPayment');


            Route::get('createVendorInvoice', 'FinVendorsController@createVendorInvoice')->name('fin-vendors.createVendorInvoice');
            Route::post('storeVendorInvoice', 'FinVendorsController@storeVendorInvoice')->name('fin-vendors.storeVendorInvoice');
            Route::get('VendorInvoiceList', 'FinVendorsController@VendorInvoiceList')->name('fin-vendors.VendorInvoiceList');
            Route::get('VendorInvoiceEditForm/{ttr}', 'FinVendorsController@VendorInvoiceEditForm')->name('fin-vendors.VendorInvoiceEditForm');
            Route::get('ViewVendorInvoice/{ttr}', 'FinVendorsController@ViewVendorInvoice')->name('fin-vendors.ViewVendorInvoice');
            Route::patch('VendorInvoiceUpdate/{ttr}', 'FinVendorsController@VendorInvoiceUpdate')->name('fin-vendors.VendorInvoiceUpdate');
            Route::get('VendorInvoiceDestroy/{ttr}', 'FinVendorsController@VendorInvoiceDestroy')->name('fin-vendors.VendorInvoiceDestroy');
            Route::get('getVendorInvoices', 'FinVendorsController@getVendorInvoices')->name('fin-vendors.getVendorInvoices');
            Route::get('getInvoiceAmount/{id}', 'FinVendorsController@getInvoiceAmount')->name('fin-vendors.getInvoiceAmount');
            Route::get('openVendorForm', 'FinVendorsController@openVendorForm');
         
            Route::post('approveVendorInvoice', 'FinVendorsController@approveVendorInvoice');
            Route::get('payments', 'FinVendorsController@payments')->name('fin-vendors.payments');
            Route::get('recievePayment/{id}', 'FinVendorsController@recievePayment')->name('fin-vendors.recievepayment');
            Route::post('storeRecievedPayment', 'FinVendorsController@storeRecievedPayment')->name('fin-vendors.storeRecievedPayment');
            Route::get('getChequeNum', 'FinVendorsController@getChequeNum')->name('fin-vendors.getChequeNum');


        });
        Route::group(['prefix' => 'receivables'], function () {

            Route::get('/installmentList', 'AccountsRecievableController@installmentList')->name('fin-recievable.installmentList');
            Route::get('/viewInstallmentDetail', 'AccountsRecievableController@viewInstallmentDetail')->name('fin-recievable.viewInstallmentDetail');
            Route::get('/createRecievableJv', 'AccountsRecievableController@createRecievableJv')->name('fin-recievable.createRecievableJv');
            Route::post('/storeRecievableJv', 'AccountsRecievableController@storeRecievableJv')->name('fin-recievable.storeRecievableJv');
            Route::get('/viewLoanStatement', 'AccountsRecievableController@viewLoanStatement')->name('fin-recievable.viewLoanStatement');
            Route::post('/viewLoanStatementDetail', 'AccountsRecievableController@viewLoanStatementDetail')->name('fin-recievable.viewLoanStatementDetail');
            Route::get('/viewApprovedInstallments', 'AccountsRecievableController@viewApprovedInstallments')->name('fin-recievable.viewApprovedInstallments');
        });


    });
    //Loans
    Route::group(['prefix' => 'aml'], function () {
        Route::get('/', 'AmlBlacklistController@index');
        //Route::get('scan_aml', 'AmlBlacklistController@index')->name('aml-blacklists.scan_aml');
        Route::get('create', 'AmlBlacklistController@create')->name('aml-blacklists.create');
        Route::get('destroy/{ttr}', 'AmlBlacklistController@destroy')->name('aml-blacklists.destroy');
        Route::get('show/{ttr}', 'AmlBlacklistController@show')->name('aml-blacklists.show');
        Route::get('edit/{ttr}', 'AmlBlacklistController@edit')->name('aml-blacklists.edit');
        Route::patch('update/{ttr}', 'AmlBlacklistController@update')->name('aml-blacklists.update');
        Route::post('store', 'AmlBlacklistController@store')->name('aml-blacklists.store');

        Route::get('/scan_aml', 'AmlBlacklistController@index')->name('scan_aml');
        Route::get('/upload_aml', 'AmlBlacklistController@index')->name('upload_aml');
    });
    //Loans
//    Route::group(['prefix' => 'loans'], function () {
//        //Borrowers
//        Route::group(['prefix' => 'borrowers'], function () {
//            Route::get('/', 'LoanBorrowerController@index');
//            Route::get('index', 'LoanBorrowerController@index')->name('loan-borrowers.index');
//            Route::get('create', 'LoanBorrowerController@create')->name('loan-borrowers.create');
//            Route::get('destroy/{ttr}', 'LoanBorrower@destroy')->name('loan-borrowers.destroy');
//            Route::get('show/{ttr}', 'LoanBorrowerController@show')->name('loan-borrowers.show');
//            Route::get('edit/{ttr}', 'LoanBorrowerController@edit')->name('loan-borrowers.edit');
//            Route::patch('update/{ttr}', 'LoanBorrowerController@update')->name('loan-borrowers.update');
//            Route::post('store', 'LoanBorrowerController@store')->name('loan-borrowers.store');
//        });
//        //Kibor
//        Route::group(['prefix' => 'kibor'], function () {
//         //   Route::get('/', 'LoanKiborRateController@index');
//            Route::get('index', 'LoanKiborRateController@index')->name('loan-kibor-rates.index');
//            Route::get('create', 'LoanKiborRateController@create')->name('loan-kibor-rates.create');
//            Route::get('destroy/{ttr}', 'LoanKiborRateController@destroy')->name('loan-kibor-rates.destroy');
//            Route::get('show/{ttr}', 'LoanKiborRateController@show')->name('loan-kibor-rates.show');
//            Route::get('edit/{ttr}', 'LoanKiborRateController@edit')->name('loan-kibor-rates.edit');
//            Route::patch('update/{ttr}', 'LoanKiborRateController@update')->name('loan-kibor-rates.update');
//            Route::post('store', 'LoanKiborRateController@store')->name('loan-kibor-rates.store');
//
//        });
//        //Kibor Hisotry
//        Route::group(['prefix' => 'kiborhistory'], function () {
//            Route::get('/', 'LoanKiborHistoryController@index');
//            Route::get('index', 'LoanKiborHistoryController@index')->name('loan-kibor-histories.index');
//            Route::get('create/{ttr}', 'LoanKiborHistoryController@create')->name('loan-kibor-histories.create');
//            Route::get('destroy/{ttr}', 'LoanKiborHistoryController@destroy')->name('loan-kibor-histories.destroy');
//            Route::get('show/{ttr}', 'LoanKiborHistoryController@show')->name('loan-kibor-histories.show');
//            Route::get('edit/{ttr}', 'LoanKiborHistoryController@edit')->name('loan-kibor-histories.edit');
//            Route::patch('update/{ttr}', 'LoanKiborHistoryController@update')->name('loan-kibor-histories.update');
//            Route::post('store', 'LoanKiborHistoryController@store')->name('loan-kibor-histories.store');
//        });
//        //takaful
//        Route::group(['prefix' => 'takaful'], function () {
//            Route::get('/', 'LoanTakafulController@index');
//            Route::get('index', 'LoanTakafulController@index')->name('loan-takafuls.index');
//            Route::get('create', 'LoanTakafulController@create')->name('loan-takafuls.create');
//            Route::get('destroy/{ttr}', 'LoanTakafulController@destroy')->name('loan-takafuls.destroy');
//            Route::get('show/{ttr}', 'LoanTakafulController@show')->name('loan-takafuls.show');
//            Route::get('edit/{ttr}', 'LoanTakafulController@edit')->name('loan-takafuls.edit');
//            Route::patch('update/{ttr}', 'LoanTakafulController@update')->name('loan-takafuls.update');
//            Route::post('store', 'LoanTakafulController@store')->name('loan-takafuls.store');
//        });
//        //Loans
//        Route::get('/', 'LoansController@borrowers')->name('tt.borrowers');
//        Route::get('/kibor', 'LoanKiborRateController@index')->name('kibor.all');
//        Route::get('/kiborhistory', 'LoanKiborHistoryController@index')->name('kibor.history');
//        Route::get('/takaful', 'LoanTakafulController@index')->name('takaful');
//        Route::get('/loandetails', 'LoansController@loandetails')->name('tt.loandetails');
//        Route::get('/', 'LoansController@index')->name('tt.borrowers');
//        Route::get('show_schedule/{ttr}', 'LoansController@show_schedule')->name('ttr.show_schedule');
//
//        Route::get('legal_docs', 'LoansController@LegalDocuments')->name('ttr.legal_docs');
//        Route::get('welcome_letter', 'LoansController@WelcomeLetter')->name('ttr.welcome_letter');
//        Route::get('acknowledgement_letter', 'LoansController@AcknowledgementLetter')->name('ttr.acknowledgement_letter');
//
//        Route::get('showloan/{ttr}', 'LoansController@showloan')->name('ttr.showloan');
//        Route::get('gen_schedule/{ttr}', 'LoansController@gen_schedule')->name('ttr.gen_schedule');
//        Route::get('loanstep/{ttr}', 'LoansController@loanstep')->name('ttr.loanstep');
//        Route::get('taxcertificate/{ttr}', 'LoansController@taxcertificate')->name('loans.taxcertificate');
//        Route::get('menu/{ttr}', 'LoansController@menuoptions')->name('loans.menu');
//        Route::get('storestep/{ttr}', 'LoansController@loanstepStore')->name('storestep');
//        Route::post('storestepkibor', 'LoansController@loanstepStoreKibor')->name('storestepkibor');
//        Route::get('losdata', 'LoansController@losdata')->name('loans.losdata');
//        Route::post('losdataByUser', 'LoansController@losdataByUser')->name('loans.losdataby_user');
//        Route::post('uploadAcknowledgementDocs/{ttr}', 'LoanDocController@uploadAcknowledgementDocs')->name('loans.upload_acknowledgement_docs');
//        Route::get('uploadAcknowledgementDocForm/{ttr}', 'LoanDocController@uploadAcknowledgementDocForm')->name('loans.upload_acknowledgement_form');
//        Route::post('storestep', 'LoansController@loanstepStore')->name('storestep');
//
//        Route::get('pay_installment/{ttr}', 'LoanPaymentRecoveredController@pay_installment')->name('loans.pay');
//        Route::get('early_settlement/{ttr}', 'LoanPaymentRecoveredController@pay_ealrysettlement')->name('loans.early');
//        Route::get('partial/{ttr}', 'LoanPaymentRecoveredController@pay_partial')->name('loans.partial');
//        Route::post('store_partial', 'LoanPaymentRecoveredController@store_partial')->name('loan-payment-recovereds.store_partial');
//        Route::get('runkibor/{ttr}', 'LoanPaymentRecoveredController@runKiborRevision')->name('runkibor');
//
//        Route::get('takaful/{ttr}', 'LoansController@takafulreport')->name('loans.takaful');
//
//
//
//        Route::get('generateWelcomeLetter/{ttr}', 'LoanDocController@generateWelcomeLetter')->name('loans.generate_welcome_letter');
//        Route::get('generateAcknowledgementLetter/{ttr}', 'LoanDocController@generateAcknowledgementLetter')->name('loans.generate_acknowledgement_letter');
//        Route::get('AgreetoMortgage/{ttr}', 'LoanDocController@AgreetoMortgage')->name('loans.agree_to_mortgage');
//        Route::get('depositeDeeds/{ttr}', 'LoanDocController@depositeDeeds')->name('loans.deposite_deeds');
//        Route::get('UndertakingIndemnity/{ttr}', 'LoanDocController@UndertakingIndemnity')->name('loans.undertaking_indemnity');
//        Route::get('UnderTakingFirstTime/{ttr}', 'LoanDocController@UnderTakingFirstTime')->name('loans.undertaking_firsttime');
//        Route::get('musharakaAgree/{ttr}', 'LoanDocController@musharakaAgree')->name('loans.musharaka_agree');
//        Route::get('promiseLease/{ttr}', 'LoanDocController@promiseLease')->name('loans.promise_lease');
//        Route::get('paymentAgree/{ttr}', 'LoanDocController@paymentAgree')->name('loans.payment_agree');
//        Route::get('purchaseUnderTakingLetters/{ttr}', 'LoanDocController@purchaseUnderTakingLetters')->name('loans.purchase_underTaking_letters');
//
//
//    });
//    //Payments
//    Route::group(['prefix' => 'payment'], function () {
//        Route::group(['prefix' => 'slips'], function () {
//            Route::get('/', 'LoanBankSlipController@index');
//            Route::get('/', 'LoanBankSlipController@index')->name('loan-bankslips.index');
//            Route::get('create', 'LoanBankSlipController@create')->name('loan-bankslips.create');
//            Route::get('destroy/{ttr}', 'LoanBankSlipController@destroy')->name('loan-bankslips.destroy');
//            Route::get('show/{ttr}', 'LoanBankSlipController@show')->name('loan-bankslips.show');
//            Route::get('edit/{ttr}', 'LoanBankSlipController@edit')->name('loan-bankslips.edit');
//            Route::patch('update/{ttr}', 'LoanBankSlipController@update')->name('loan-bankslips.update');
//            Route::post('store', 'LoanBankSlipController@store')->name('loan-bankslips.store');
//        });
//        Route::group(['prefix' => 'due'], function () {
//            Route::get('/', 'LoanPaymentDueController@index');
//            Route::get('/', 'LoanPaymentDueController@index')->name('loan-payment-dues.index');
//            Route::get('create', 'LoanPaymentDueController@create')->name('loan-payment-dues.create');
//            Route::get('destroy/{ttr}', 'LoanPaymentDueController@destroy')->name('loan-payment-dues.destroy');
//            Route::get('show/{ttr}', 'LoanPaymentDueController@show')->name('loan-payment-dues.show');
//            Route::get('edit/{ttr}', 'LoanPaymentDueController@edit')->name('loan-payment-dues.edit');
//            Route::patch('update/{ttr}', 'LoanPaymentDueController@update')->name('loan-payment-dues.update');
//            Route::post('store', 'LoanPaymentDueController@store')->name('loan-payment-dues.store');
//        });
//        Route::group(['prefix' => 'recovered'], function () {
//            Route::get('/', 'LoanPaymentRecoveredController@index');
//            Route::get('/', 'LoanPaymentRecoveredController@index')->name('loan-payment-recovereds.index');
//            Route::get('create', 'LoanPaymentRecoveredController@create')->name('loan-payment-recovereds.create');
//            Route::post('store', 'LoanPaymentRecoveredController@store')->name('loan-payment-recovereds.store');
//            Route::post('storepay', 'LoanPaymentRecoveredController@storepay')->name('loan-payment-recovereds.storepay');
//            Route::post('earlypay', 'LoanPaymentRecoveredController@earlypay')->name('loan-payment-recovereds.earlypay');
//            Route::get('destroy/{ttr}', 'LoanPaymentRecoveredController@destroy')->name('loan-payment-recovereds.destroy');
//            Route::get('show/{ttr}', 'LoanPaymentRecoveredController@show')->name('loan-payment-recovereds.show');
//            Route::get('edit/{ttr}', 'LoanPaymentRecoveredController@edit')->name('loan-payment-recovereds.edit');
//            Route::patch('update/{ttr}', 'LoanPaymentRecoveredController@update')->name('loan-payment-recovereds.update');
//        });
//
//
//
//    });

    Route::group(['prefix' => 'fixedassets'], function () {

        Route::group(['prefix' => 'items'], function () {
            Route::get('/', 'ItemsController@index')->name('items.all');
            Route::get('/create', 'ItemsController@create')->name('items.create');
            Route::post('store', 'ItemsController@store')->name('items.store');
            Route::get('edit/{id}', 'ItemsController@edit')->name('items.edit');
            Route::patch('update/{id}', 'ItemsController@update')->name('items.update');
            Route::get('destroy/{id}', 'ItemsController@destroy')->name('items.destroy');

        });
        Route::group(['prefix' => 'subitems'], function () {
            Route::get('/', 'SubItemsController@index')->name('subitems.all');
            Route::get('/create', 'SubItemsController@create')->name('subitems.create');
            Route::post('store', 'SubItemsController@store')->name('subitems.store');
            Route::get('edit/{id}', 'SubItemsController@edit')->name('subitems.edit');
            Route::patch('update/{id}', 'SubItemsController@update')->name('subitems.update');
            Route::get('destroy/{id}', 'SubItemsController@destroy')->name('subitems.destroy');
            Route::get('getItemCode', 'SubItemsController@getItemCode')->name('subitems.getItemCode');

        });

        Route::group(['prefix' => 'registration'], function () {
            Route::get('/', 'AssetRegistrationController@index')->name('asset-registration.all');
            Route::get('/create', 'AssetRegistrationController@create')->name('asset-registration.create');
            Route::post('store', 'AssetRegistrationController@store')->name('asset-registration.store');
            Route::get('edit/{id}', 'AssetRegistrationController@edit')->name('asset-registration.edit');
            Route::patch('update/{id}', 'AssetRegistrationController@update')->name('asset-registration.update');
            Route::get('destroy/{id}', 'AssetRegistrationController@destroy')->name('asset-registration.destroy');
            Route::get('renewSession', 'AssetRegistrationController@renewSession')->name('asset-registration.renewSession');
            Route::post('renewSessionDetail;', 'AssetRegistrationController@renewSessionDetail')->name('asset-registration.renewSessionDetail');

            Route::get('decreaseAssetAmountForm/{id}', 'AssetRegistrationController@decreaseAssetAmountForm')->name('asset-registration.decreaseForm');
            Route::post('assetImpairmentDetail/{id}', 'AssetRegistrationController@assetImpairmentDetail')->name('asset-registration.assetImpairmentDetail');

        });



    });
    Route::group(['prefix' => 'leaseobligations'], function () {
        Route::get('/', 'LeaseObligationController@index')->name('lease-obligations.all');
        Route::get('/create', 'LeaseObligationController@create')->name('lease-obligations.create');
        Route::post('store', 'LeaseObligationController@store')->name('lease-obligations.store');
        Route::get('edit/{id}', 'LeaseObligationController@edit')->name('lease-obligations.edit');
        Route::patch('update/{id}', 'LeaseObligationController@update')->name('lease-obligations.update');
        Route::get('destroy/{id}', 'LeaseObligationController@destroy')->name('lease-obligations.destroy');
        Route::get('renewSession', 'LeaseObligationController@renewSession')->name('lease-obligations.renewSession');
        Route::post('renewSessionDetail;', 'LeaseObligationController@renewSessionDetail')->name('lease-obligations.renewSessionDetail');


    });


    /*     * ************* Support Team **************** */
    Route::group(['namespace' => 'SupportTeam',], function () {

        /*         * ************* Students **************** */
//        Route::group(['prefix' => 'students'], function () {
//            Route::get('reset_pass/{st_id}', 'StudentRecordController@reset_pass')->name('st.reset_pass');
//            Route::get('graduated', 'StudentRecordController@graduated')->name('students.graduated');
//            Route::put('not_graduated/{id}', 'StudentRecordController@not_graduated')->name('st.not_graduated');
//            Route::get('list/{class_id}', 'StudentRecordController@listByClass')->name('students.list');
//
//            /* Promotions */
//            Route::post('promote_selector', 'PromotionController@selector')->name('students.promote_selector');
//            Route::get('promotion/manage', 'PromotionController@manage')->name('students.promotion_manage');
//            Route::get('promotion/reset/{pid}', 'PromotionController@reset')->name('students.promotion_reset');
//            Route::get('promotion/reset_all', 'PromotionController@reset_all')->name('students.promotion_reset_all');
//            Route::get('promotion/{fc?}/{fs?}/{tc?}/{ts?}', 'PromotionController@promotion')->name('students.promotion');
//            Route::post('promote/{fc}/{fs}/{tc}/{ts}', 'PromotionController@promote')->name('students.promote');
//        });

        /*         * ************* Users **************** */
//        Route::group(['prefix' => 'users'], function () {
//            Route::get('reset_pass/{id}', 'UserController@reset_pass')->name('users.reset_pass');
//        });
        /*         * ************* Payments **************** */
//        Route::group(['prefix' => 'payments'], function () {
//
//            Route::get('manage/{class_id?}', 'PaymentController@manage')->name('payments.manage');
//            Route::get('invoice/{id}/{year?}', 'PaymentController@invoice')->name('payments.invoice');
//            Route::get('receipts/{id}', 'PaymentController@receipts')->name('payments.receipts');
//            Route::get('pdf_receipts/{id}', 'PaymentController@pdf_receipts')->name('payments.pdf_receipts');
//            Route::post('select_year', 'PaymentController@select_year')->name('payments.select_year');
//            Route::post('select_class', 'PaymentController@select_class')->name('payments.select_class');
//            Route::get('reset_record/{id}', 'PaymentController@reset_record')->name('payments.reset_record');
//            Route::post('pay_now/{id}', 'PaymentController@pay_now')->name('payments.pay_now');
//        });

        /*         * ************* Pins **************** */
//        Route::group(['prefix' => 'pins'], function () {
//            Route::get('create', 'PinController@create')->name('pins.create');
//            Route::get('/', 'PinController@index')->name('pins.index');
//            Route::post('/', 'PinController@store')->name('pins.store');
//            Route::get('enter/{id}', 'PinController@enter_pin')->name('pins.enter');
//            Route::post('verify/{id}', 'PinController@verify')->name('pins.verify');
//            Route::get('/', 'PinController@destroy')->name('pins.destroy');
//        });
    });

//      Route::group(['prefix' => 'ajax'], function () {
//        Route::get('get_lga/{state_id}', 'AjaxController@get_lga')->name('get_lga');
//        Route::get('get_class_sections/{class_id}', 'AjaxController@get_class_sections')->name('get_class_sections');
//        Route::get('get_class_subjects/{class_id}', 'AjaxController@get_class_subjects')->name('get_class_subjects');
//    });


    Route::get('/addMainMenu', 'MyAccountController@addMainMenu');
    Route::post('/storeMainMenu', 'MyAccountController@storeMainMenu');

    Route::get('/addSubMenu', 'MyAccountController@addSubMenu');
    Route::post('/storeSubMenu', 'MyAccountController@storeSubMenu');

    Route::get('/addRoles', 'MyAccountController@addRoles');
    Route::post('/storeRoles', 'MyAccountController@storeRoles');
    Route::get('/viewRoles', 'MyAccountController@viewRoles');
    Route::get('/deleteRoles/{id}', 'MyAccountController@deleteRoles');

    Route::get('/addUsers', 'MyAccountController@addUsers');
    Route::post('/storeUsers', 'MyAccountController@storeUsers');

    Route::get('/viewUsers', 'MyAccountController@viewUsers');
    Route::get('/editUsers/{id}', 'MyAccountController@editUsers');
    Route::post('/updateUsers/{id}', 'MyAccountController@updateUsers');
    Route::post('/getUsers', 'MyAccountController@getUsers');

    Route::post('/storeLogs', 'MyAccountController@storeLogs');
    Route::get('/PageActivity', 'MyAccountController@PageActivity');
    Route::get('/userPageActivity', 'MyAccountController@userPageActivity');
    Route::get('/loginActivity', 'MyAccountController@loginActivity');
    Route::get('/loginPageActivity', 'MyAccountController@loginPageActivity');



});

/* * ********************** SUPER ADMIN *************************** */
Route::group(['namespace' => 'SuperAdmin', 'middleware' => 'super_admin', 'prefix' => 'super_admin'], function () {

    Route::get('/settings', 'SettingController@index')->name('settings');
    Route::put('/settings', 'SettingController@update')->name('settings.update');
});

/* * ********************** PARENT *************************** */
Route::group(['namespace' => 'MyParent', 'middleware' => 'my_parent',], function () {

    Route::get('/my_children', 'MyController@children')->name('my_children');
});
