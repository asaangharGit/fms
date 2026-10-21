<?php

namespace App\Http\Controllers;

use App\Models\AssetDepreciation;
use App\Models\AssetImpairment;
use App\Models\AssetRegistration;
use App\Models\FinChartOfAccount;
use App\Models\FinGeneralLedger;
use App\Models\FinGeneralLedgerDetail;
use App\Models\FinVendorInvoice;
use App\Models\FinVendors;
use App\Models\Items;
use App\Models\LeaseObligationDetail;
use App\Models\LeaseObligations;
use App\Models\LoanList;
use App\Models\RecievableAmount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Qs;

class ReportsController extends Controller {

    public function __construct() {

    }

    public function TrialBalance() {
        //dd("Yeah");
        $d['report'] = "Financial";
        return view('reports.trialreport', $d);
    }

    public function TrialBalanceReport(Request $request) {
        error_reporting(E_ERROR);
        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date'
                ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;
        $chart = ['01'=>'Assets','02'=>'Liabilities','03'=>'Equity','04'=>'Expense','05'=>'Income'];

        $d['chart'] = $chart;


        return view('reports.trialreport', $d);
        //dd($request->all());
    }

    public function trialSumReportForm() {
        //dd("Yeah");
        $d['report'] = "Financial";
        return view('reports.trialSumReportForm', $d);
    }

//    public function trialSumReportDetail(Request $request) {
//        error_reporting(E_ERROR);
//        $this->validate($request, [
//            'datefrom' => 'required|date',
//            'dateto' => 'required|date'
//        ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);
//
//        $d['datef'] = $request->datefrom;
//        $d['datet'] = $request->dateto;
//        $d['i'] = 0;
//        $ReportData = DB::table("fin_general_ledgers as gl")
//            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
//            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
//
//            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
//            //    ->where('gld.coa_id','172')
//            ->select(DB::raw("
//                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
//                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
//            ->groupBy('fca_l5.code')
//            ->get();
//
//
//        $d['ReportData'] = $ReportData;
//
//        return view('reports.trial2', $d);
//        //dd($request->all());
//    }

    public function trialSumReportDetail(Request $request) {
        error_reporting(E_ERROR);
        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date'
        ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;
        $assets = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date", '<=', $request->dateto)
            ->where('fca_l5.parent_code','like','01%')
            ->where('voucher_status','3')

            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))

            ->groupBy('fca_l5.code')
            ->get();

        $liabilities = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date", '<=', $request->dateto)
            ->where('fca_l5.parent_code','like','02%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();

        $equity = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date", '<=', $request->dateto)
            ->where('fca_l5.parent_code','like','03%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();
        $expense = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
            ->where('fca_l5.parent_code','like','04%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();
        $income = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
            ->where('fca_l5.parent_code','like','05%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();

        $expense_close = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date",'<',$request->datefrom)
            ->where('fca_l5.parent_code','like','04%')
            ->where('voucher_status','3')
            ->select(DB::raw("SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $income_close = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date",'<',$request->datefrom)
            ->where('fca_l5.parent_code','like','05%')
            ->where('voucher_status','3')
            ->select(DB::raw("SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $expense_prev = $expense_close->sum('gld.debit')- $expense_close->sum('gld.credit');

          $income_prev  = $income_close->sum('gld.debit') - $income_close->sum('gld.credit');
        //echo  abs($income_prev)-$expense_prev;;die;
        $d['income_prev'] = $income_prev;
        $d['expense_prev'] = $expense_prev;
        $d['assets'] = $assets;
        $d['liabilities'] = $liabilities;
        $d['equity'] = $equity;
        $d['expense'] = $expense;
        $d['income'] = $income;

        return view('reports.trial2', $d);
        //dd($request->all());
    }

    public function Financial() {
        //dd("Yeah");
        $d['report'] = "Financial";
        $d['chartofaccount_id'] = "";
        $d['chartofaccount_id2'] = "";
        $d['datef'] = date('Y-m-d');
        $d['datet'] =  date('Y-m-d');
        $d['dd'] = array();
        $d['chartOfAccounts'] = \App\Models\FinChartOfAccount::where('level', 'L5')->select('id', 'title', 'code')->orderBy("code")->get();

        return view('reports.finreport', $d);
    }
    public function generalLegderReportDetail(Request $request) {

        //dd($request->all());
        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date' ],
            [],
            ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['chartOfAccounts'] = \App\Models\FinChartOfAccount::where('level', 'L5')->select('id', 'title', 'code')->orderBy("code")->get();
        $d['chartofaccount_id'] = $request->chartofaccount;
        $d['chartofaccount_id2'] = $request->chartofaccount2;
        $d['i'] = 0;


           //  $dd = FinChartOfAccount::where('level','L5')->orderBy('code')->get();
         $dd = FinChartOfAccount::whereBetween("code", array($request->chartofaccount, $request->chartofaccount2), "and")->where('level','L5')->orderBy('code')->get();
//        if($request->chartofaccount){
//             $dd = FinChartOfAccount::where('id',$request->chartofaccount)->where('level','L5')->orderBy('code')->get();
//        }

        $d['dd'] = $dd;
        return view('reports.finreport', $d);
        //dd($request->all());
    }
    public function vendorLedgerReportForm() {
        //dd("Yeah");
        $d['invoices'] = FinVendorInvoice::where('status',1)->get();
        $d['invoices_id'] = "";
        $d['datef'] = date('Y-m-d');
        $d['datet'] = date('Y-m-d');
        $d['ReportData'] = array();

        return view('reports.vendorLedgerReportForm', $d);
    }
    public function vendorLedgerReportDetail(Request $request) {

        //dd($request->all());
        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date' ],
            [],
            ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['invoices'] = FinVendorInvoice::where('status',1)->get();
        $d['invoices_id'] =$request->invoice_id;
        $d['i'] = 0;


            $ReportData = FinGeneralLedger::where('invoices_id',$request->invoice_id)
                ->select([
                    DB::raw("SUM(debit) as total_debit"),
                    DB::raw("SUM(credit) as total_credit"),
                    DB::raw("invoices_id,details,invoices_id,vendor_id"),
                    ]
                )
                ->groupBy('invoices_id')
                ->get();


        //dd($raja_array);
        $d['ReportData'] = $ReportData;

        return view('reports.vendorLedgerReportForm', $d);
        //dd($request->all());
    }
    public function profitLossReportForm(Request $request){
        $d['datefrom'] ='' ;
        $d['dateto'] = '';
        return view('reports.profitLossReportForm',$d);


    }

    public static function getProfitLossDetail($request){
        $d['datefrom'] = $request->datefrom;
        $d['dateto'] = $request->dateto;


        $expense_1 = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
            ->where('fca_l5.parent_code','like','04-01%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $expense_2 = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
            ->where('fca_l5.parent_code','like','04-02%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $ProvisionForIncomeTax = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
            ->where('fca_l5.parent_code','04-02-001-019')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"));



        $income = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
            ->where('fca_l5.parent_code','like','05%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();
         $d['income']    = $income;
         $d['expense_1'] = $expense_1->sum('gld.debit')- $expense_1->sum('gld.credit');
         $d['expense_2'] = $expense_2->sum('gld.debit')- $expense_2->sum('gld.credit');
         $d['ProvisionForIncomeTax'] = $ProvisionForIncomeTax->sum('gld.debit')- $ProvisionForIncomeTax->sum('gld.credit');

        return $d;

    }
    public static function getProfitLossMonthwiseDetail($request){
        $d['datefrom'] = $request->datefrom;
        $d['dateto'] = $request->dateto;
        $d['monthly'] = $request->monthly;
        $expense_L2_1_acc =  \App\Models\FinChartOfAccount::Where('parent_code', 'like', '04-01%')->select('id')->get()->toArray();
        $expense_L2_2_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '04-02%')->select('id')->orderBy("code")->get()->toArray();

        $income_L2_1_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '05-01%')->select('id')->get()->toArray();
        $income_L2_2_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '05-02%')->select('id')->get()->toArray();
        $income_L2_3_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '05-03%')->select('id')->get()->toArray();
        $income_L2_4_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '05-04%')->select('id')->get()->toArray();
        $income_L2_5_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '05-05%')->select('id')->get()->toArray();
        $income_L2_6_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '05-06%')->select('id')->get()->toArray();
        $other_income_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '05-07%')->select('id')->get()->toArray();
        $taxation_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '01-02-003-002%')->select('id')->get()->toArray();
        $financial_charges_acc = \App\Models\FinChartOfAccount::Where('parent_code', 'like', '04-02-001-021%')->select('id')->get()->toArray();

        $expense_L2_1 =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$expense_L2_1_acc)
            ->where('voucher_status','3')
            ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
            ->sum('fin_general_ledger_details.debit');

//        $expense_L2_2 =  DB::table("fin_general_ledgers")
//            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
//            ->whereIn('coa_id',$expense_L2_2_acc)
//            ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
//            ->sum('fin_general_ledger_details.debit');
        $expense_L2_2 = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->where('voucher_status','3')
            ->whereIn('coa_id',$expense_L2_2_acc)
            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
          //  ->select(DB::raw("gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->get();
        $income_L2_1 =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$income_L2_1_acc)
            ->where('voucher_status','3')
            ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
            ->sum('fin_general_ledger_details.credit');
        $income_L2_2 =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$income_L2_2_acc)
            ->where('voucher_status','3')
            ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
            ->sum('fin_general_ledger_details.credit');
        $income_L2_3 =  DB::table("fin_general_ledgers")
            ->where('voucher_status','3')
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$income_L2_3_acc)->sum('fin_general_ledger_details.credit');

        $income_L2_4 =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$income_L2_4_acc)
            ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
            ->sum('fin_general_ledger_details.credit');

        $income_L2_5 =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$income_L2_5_acc) ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
            ->sum('fin_general_ledger_details.credit');

        $income_L2_6 =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$income_L2_6_acc)
            ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
            ->sum('fin_general_ledger_details.credit');

        $other_income =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$other_income_acc)
            ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
            ->sum('fin_general_ledger_details.credit');

        $financial_charges =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$financial_charges_acc)
            ->where('voucher_status','3')
            ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
            ->sum('fin_general_ledger_details.debit');

        $taxation =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->whereIn('coa_id',$taxation_acc)
            ->where('voucher_status','3')
            ->whereBetween("fin_general_ledgers.txn_date", array($request->datefrom, $request->dateto), "and")
            ->sum('fin_general_ledger_details.debit');



        $d['expense_L2_1'] =$expense_L2_1;
        $d['expense_L2_2'] =$expense_L2_2;
        $d['income_L2_1'] =$income_L2_1;
        $d['income_L2_2'] =$income_L2_2;
        $d['income_L2_3'] =$income_L2_3;
        $d['income_L2_4'] =$income_L2_4;
        $d['income_L2_5'] =$income_L2_5;
        $d['income_L2_6'] =$income_L2_6;
        $d['other_income'] =$other_income;
        $d['financial_charges'] =$financial_charges;
        $d['taxation'] =$taxation;

        return $d;

    }

    public function profitLossReportDetail(Request $request) {

        if($request->monthly == 'yes'){

            $d = static::getProfitLossMonthwiseDetail($request);
            return view('reports.profitLossMonthwiseReportForm', $d);
        }else{
            $d = static::getProfitLossDetail($request);
            return view('reports.profitLossReportForm', $d);

        }

    }


    public function BalancetSheetReportForm (Request $request) {

        $d['datefrom'] ='' ;
        $d['dateto'] = '';
        $d['usd_rate'] = 1;
        return view('reports.BalancetSheetReportForm',$d);
    }



    public function BalancetSheetReportDetail(Request $request) {

        $d['datefrom'] = $request->datefrom;
        $d['dateto'] = $request->dateto;
        $d['usd_rate'] = 1;
        if(isset($request->usd_rate)){

            $d['usd_rate'] = $request->usd_rate;
        }


        $equity_1_detail =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->join("fin_chart_of_accounts", "fin_chart_of_accounts.id", "=", "fin_general_ledger_details.coa_id")
            ->where('fin_chart_of_accounts.parent_code', 'like', '03-01-001%')
            ->where('voucher_status','3')
           // ->whereDate("fin_general_ledgers.txn_date",'<=', $request->datefrom)
            ->whereDate("fin_general_ledgers.txn_date", '<=', $request->dateto)
            ->select(DB::raw("SUM(fin_general_ledger_details.debit) as debit,SUM(fin_general_ledger_details.credit) as credit"))
            ->get();

        $equity_1 = array_sum(array_column($equity_1_detail->toArray(), 'debit'))-array_sum(array_column($equity_1_detail->toArray(), 'credit'));

        $equity_3_detail =  DB::table("fin_general_ledgers")
            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
            ->join("fin_chart_of_accounts", "fin_chart_of_accounts.id", "=", "fin_general_ledger_details.coa_id")
            ->where('fin_chart_of_accounts.parent_code', 'like', '03-01-003%')
            ->where('voucher_status','3')
         //   ->whereDate("fin_general_ledgers.txn_date",'<=', $request->datefrom)
            ->whereDate("fin_general_ledgers.txn_date", '<=', $request->dateto)
            ->select('fin_general_ledger_details.credit','fin_general_ledger_details.debit')
            ->get();

        $equity_3 = array_sum(array_column($equity_3_detail->toArray(), 'debit'))-array_sum(array_column($equity_3_detail->toArray(), 'credit'));


        $expense_cal_current = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->where('voucher_status','3')
            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
            ->where('fca_l5.parent_code','like','04%')
            ->select(DB::raw("SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $income_cal_current = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->where('voucher_status','3')
            ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
            ->where('fca_l5.parent_code','like','05%')
            ->select(DB::raw("SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $expense_current_prev = $expense_cal_current->sum('gld.debit')- $expense_cal_current->sum('gld.credit');
        $income_current_prev  = $income_cal_current->sum('gld.debit') - $income_cal_current->sum('gld.credit');

        $expense_cal = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date",'<', $request->datefrom)
            ->where('fca_l5.parent_code','like','04%')
            ->where('voucher_status','3')
            ->select(DB::raw("SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $income_cal = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date",'<', $request->datefrom)
            ->where('fca_l5.parent_code','like','05%')
            ->where('voucher_status','3')
            ->select(DB::raw("SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $expense_prev = $expense_cal->sum('gld.debit')- $expense_cal->sum('gld.credit');
        $income_prev  = $income_cal->sum('gld.debit') - $income_cal->sum('gld.credit');


        $non_current_liabilities = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date", '<=', $request->dateto)
            ->where('fca_l5.parent_code','like','02-01%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();
        $non_current_asset_1 = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date", '<=', $request->dateto)
            ->where('fca_l5.parent_code','like','01-01-001-001%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"));
        $non_current_asset_2 = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date", '<=', $request->dateto)
            ->where('fca_l5.parent_code','like','01-01-001-002%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"));


        $current_lib_acc = FinChartOfAccount::where('parent_code','like','02-02%')->where('level','L4')->get();

        $non_current_asset_acc = FinChartOfAccount::where('parent_code','like','01-01%')->where('level','L4')->get();

        $current_asset_acc = FinChartOfAccount::where('parent_code','like','01-02%')->where('level','L4')->get();

        $d['non_current_liabilities'] = $non_current_liabilities;
        $d['non_current_asset_acc'] = $non_current_asset_acc;
        $d['current_lib_acc'] = $current_lib_acc;
        $d['non_current_asset_1'] = $non_current_asset_1;
        $d['non_current_asset_2'] = $non_current_asset_2;
        $d['current_asset_acc'] = $current_asset_acc;
        $d['equity_1'] = $equity_1;
        $d['equity_3'] = $equity_3;
        $d['p_l'] = abs($income_prev)-abs($expense_prev);
        $d['p_l_current'] = abs($income_current_prev)-abs($expense_current_prev);


        return view('reports.BalancetSheetReportForm', $d);
    }


    public function Dues() {
        //dd("Yeah");
        $d['report'] = "Dues";
        return view('reports.dues', $d);
    }

    public function DuesReport(Request $request) {
        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date'
                ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;

        $ReportData = DB::table("loan_history")
                ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
                ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
                ->join("general_offices", "general_offices.id", "=", "loan_history.office_id")
                ->where("loan_payment_due.payment_status", "=", "0")
                ->groupBy("loan_history.id")
                ->select(DB::raw(""
                . "concat(loan_borrowers.fname,' ',loan_borrowers.mname,' ', loan_borrowers.lname) as 'b_name', "
                . "general_offices.name, "
                . "sum(loan_payment_due.amount_pr) as 's_am_pr', "
                . "sum(loan_payment_due.amount_mu) as 's_am_mu',"
                . "sum(if(loan_payment_due.due_date between '" . ($request->datefrom) . "' and '" . ($request->dateto) . "',loan_payment_due.amount_pr,0 )) as 'c_am_pr', "
                . "sum(if(loan_payment_due.due_date between '" . ($request->datefrom) . "' and '" . ($request->dateto) . "',loan_payment_due.amount_mu,0 )) as 'c_am_mu' "
                . ""), "loan_history.*")
                ->get();
        $d['ReportData'] = $ReportData;
        //dd($ReportData);
//        $loanBankslips = DB::table('loan_bankslips')
//            ->join('loan_payment_recovereds', 'loan_payment_recovereds.bank_slip_id', '=', 'loan_bankslips.id')
//            ->join('fin_banks_accounts', 'fin_banks_accounts.id', '=', 'loan_bankslips.bankAccountId')
//            ->groupBy('loan_bankslips.id')
//            ->select('loan_bankslips.*', 'fin_banks_accounts.bank_name', DB::raw('sum(loan_payment_recovereds.amount_total) as amount_sum'))
//
//            ->get();

        return view('reports.dues', $d);
        //dd($request->all());
    }

    public function Payments() {
        //dd("Yeah");
        $d['report'] = "Dues";
        return view('reports.payments', $d);
    }

    public function PaymentsReport(Request $request) {
        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date'
                ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;

        $ReportData = DB::table("loan_history")
                ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
                ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
                ->join("general_offices", "general_offices.id", "=", "loan_history.office_id")
                ->where("loan_payment_due.payment_status", "=", "0")
                ->groupBy("loan_history.id")
                ->select(DB::raw(""
                                . "concat(loan_borrowers.fname,' ',loan_borrowers.mname,' ', loan_borrowers.lname) as 'b_name', "
                                . "general_offices.name, "
                                . "sum(loan_payment_due.amount_pr) as 's_am_pr', "
                                . "sum(loan_payment_due.amount_mu) as 's_am_mu',"
                                . "sum(if(loan_payment_due.due_date between '" . ($request->datefrom) . "' and '" . ($request->dateto) . "',loan_payment_due.amount_pr,0 )) as 'c_am_pr', "
                                . "sum(if(loan_payment_due.due_date between '" . ($request->datefrom) . "' and '" . ($request->dateto) . "',loan_payment_due.amount_mu,0 )) as 'c_am_mu' "
                                . ""), "loan_history.*")
                ->get();
        $d['ReportData'] = $ReportData;
//        dd($ReportData);
//        $loanBankslips = DB::table('loan_bankslips')
//            ->join('loan_payment_recovereds', 'loan_payment_recovereds.bank_slip_id', '=', 'loan_bankslips.id')
//            ->join('fin_banks_accounts', 'fin_banks_accounts.id', '=', 'loan_bankslips.bankAccountId')
//            ->groupBy('loan_bankslips.id')
//            ->select('loan_bankslips.*', 'fin_banks_accounts.bank_name', DB::raw('sum(loan_payment_recovereds.amount_total) as amount_sum'))
//
//            ->get();

        return view('reports.payments', $d);
        //dd($request->all());
    }


    public function assetDepreciationReportForm(){


        $category ='';
        $year = '';
        $items = Items::whereNull('parent_id')->where('status',1)->get();
        return view('reports.assetDepreciationReportForm',compact('items','category','year'));
    }


    public function assetDepreciationReportDetail(Request $request){
        $items = Items::whereNull('parent_id')->where('status',1)->get();
        $category =$request->category;
        $year = $request->year;
        if($category == 'all'):
            $detail =  Items::whereNull('parent_id')->where('status',1)->get();
        else:
            $detail =  Items::whereNull('parent_id')->where('id',$category)->where('status',1)->get();
        endif;



        return view('reports.assetDepreciationReportForm',compact('detail','items','category','year'));
    }


    public function leaseObligationForm(){

        $lease_id = '';
        $lease_list = LeaseObligations::where('status',1)->get();
        return view('reports.lease-obligation-form',compact('lease_list','lease_id'));
    }

    public function leaseObligationDetail(Request $request){

        $lease_id = $request->lease_id;
        $detail = LeaseObligationDetail::where('lease_id',$request->lease_id)->get();
        $lease_list = LeaseObligations::where('status',1)->get();
        return view('reports.lease-obligation-form',compact('detail','lease_id','lease_list'));
    }


    public function receivableReportForm(Request $request)
    {
        $datef = ($request->datefrom);
        $datet = ($request->dateto);
        $customer_id = ($request->customer_id );
        $customers = LoanList::get();
        $report_type = 'summary';
        return view("reports.receivableReportForm",compact('report_type','customer_id','customers','datef','datet'));

    }

    public function receivableReportDetail(Request $request)
    {

        $datef = ($request->datefrom) ? $request->datefrom : "2022-".date('m-d');
        $datet = ($request->dateto) ? $request->dateto : date('Y-m-d');
        $customers = LoanList::get();
        $customer_id = ($request->customer_id );
        $report_type = $request->sum_detail;



        if($request->sum_detail == 'summary'):

            $rec = LoanList::select('loan_list.name',
                DB::raw('SUM(recievable_amounts.total_amount) as total_amount'),
                DB::raw('SUM(recievable_amounts.principal) as principal'),
                DB::raw('SUM(recievable_amounts.profit) as profit'),
                DB::raw('SUM(CASE WHEN recievable_amounts.approved_by = "1" THEN recievable_amounts.total_amount ELSE 0 END) as paid_total'),
                DB::raw('SUM(CASE WHEN recievable_amounts.approved_by = "1" THEN recievable_amounts.principal ELSE 0 END) as paid_principal'),
                DB::raw('SUM(CASE WHEN recievable_amounts.approved_by = "1" THEN recievable_amounts.profit ELSE 0 END) as paid_total'))
                ->leftJoin('recievable_amounts', 'loan_list.id', '=', 'recievable_amounts.loan_id')
                ->whereBetween('recievable_amounts.recovered_date', [$datef, $datet])
                ->groupBy('loan_list.id');


        if($request->customer_id == 'show_all'):

            $rec = ($rec->get());

        else:

            $rec =  $rec->where('loan_list.id',$request->customer_id)->get();
        endif;

        return view("reports.receivableReportForm",compact('report_type','customer_id','customers','rec','datef','datet','request'));

        else:

           // $rec = RecievableAmount::with('loan')->where('loan_id',$request->customer_id)->orderBy('recovered_date')->get();
            $rec = RecievableAmount::join('loan_list', 'recievable_amounts.loan_id', '=', 'loan_list.id')
                ->select('recievable_amounts.*', 'loan_list.name')
                ->where('loan_id',$request->customer_id)
                ->orderBy('recovered_date')
                ->get();
            return view("reports.receivableReportForm",compact('report_type','customer_id','customers','rec','datef','datet','request'));

        endif;

    }



}
