<?php

namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Repositories\UserRepo;
use DB;
use GuzzleHttp\Psr7\Request;

class HomeController extends Controller
{
    protected $user;
    public function __construct(UserRepo $user)
    {
        $this->user = $user;
    }


    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function privacy_policy()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.privacy_policy', $data);
    }

    public function terms_of_use()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.terms_of_use', $data);
    }

    public function dashboard()
    {
        $d=[];
        if(Qs::userIsTeamSAT()){
            $d['users'] = $this->user->getAll();
        }

        return view('pages.support_team.dashboard', $d);
    }

    public function dashboardData(){

      $trial = array(
          'trialDebit'=>number_format(Qs::trialBalance()['debit']),
          'trialCredit'=>number_format(Qs::trialBalance()['credit']),
           'trial_total'=>number_format(Qs::trialBalance()['debit']-Qs::trialBalance()['credit']));

        $jsBank = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca", "fca.id", "=", "gld.coa_id")
            ->where("gld.coa_id",172)
            ->where('gl.voucher_status','3')
            ->select(DB::raw(""."gld.debit,gld.credit"))
            ->get()->toArray();

        $jsBankDebit = number_format((array_sum(array_column($jsBank, 'debit'))));
        $jsBankCredit = number_format((array_sum(array_column($jsBank, 'credit'))));
        $jsBankTotal = (array_sum(array_column($jsBank, 'debit')))-(array_sum(array_column($jsBank, 'credit')));
        $jsBank = array('jsBankDebit'=>$jsBankDebit,'jsBankCredit'=>$jsBankCredit,'jsBankTotal'=>number_format($jsBankTotal));

        $fbl_p = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca", "fca.id", "=", "gld.coa_id")
            ->where('gl.voucher_status','3')
            ->where("gld.coa_id",354)
            ->select(DB::raw("". "gld.debit,gld.credit"))
            ->get()->toArray();

        $fbl_pDebit = number_format((array_sum(array_column($fbl_p, 'debit'))));
        $fbl_pCredit = number_format((array_sum(array_column($fbl_p, 'credit'))));
        $fbl_pTotal = (array_sum(array_column($fbl_p, 'debit')))-(array_sum(array_column($fbl_p, 'credit')));
        $fbl_p = array('fbl_pDebit'=>$fbl_pDebit,'fbl_pCredit'=>$fbl_pCredit,'fbl_pTotal'=>number_format($fbl_pTotal));


        $fbl_e = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca", "fca.id", "=", "gld.coa_id")
            ->where("gld.coa_id",355)
            ->where('gl.voucher_status','3')
            ->select(DB::raw("" . "gld.debit,gld.credit"))
            ->get()->toArray();

        $fbl_eDebit = number_format((array_sum(array_column($fbl_e, 'debit'))));
        $fbl_eCredit = number_format((array_sum(array_column($fbl_e, 'credit'))));
        $fbl_eTotal = (array_sum(array_column($fbl_e, 'debit')))-(array_sum(array_column($fbl_e, 'credit')));
        $fbl_e = array('fbl_eDebit'=>$fbl_eDebit,'fbl_eCredit'=>$fbl_eCredit,'fbl_eTotal'=>number_format($fbl_eTotal));



        $fbl_d = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca", "fca.id", "=", "gld.coa_id")
            ->where('gl.voucher_status','3')
            ->where("gld.coa_id",356)
            ->select(DB::raw(""."gld.debit,gld.credit"))
            ->get()->toArray();

        $fbl_dDebit = number_format((array_sum(array_column($fbl_d, 'debit'))));
        $fbl_dredit = number_format((array_sum(array_column($fbl_d, 'credit'))));
        $fbl_dTotal = (array_sum(array_column($fbl_d, 'debit')))-(array_sum(array_column($fbl_d, 'credit')));
        $fbl_d = array('fbl_dDebit'=>$fbl_dDebit,'fbl_dCredit'=>$fbl_dredit,'fbl_dTotal'=>number_format($fbl_dTotal));


        $fbl_c = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca", "fca.id", "=", "gld.coa_id")
            ->where('gl.voucher_status','3')
            ->where("gld.coa_id",357)
            ->select(DB::raw(""."gld.debit,gld.credit"))
            ->get()->toArray();


        $fbl_cDebit = number_format((array_sum(array_column($fbl_c, 'debit'))));
        $fbl_credit = number_format((array_sum(array_column($fbl_c, 'credit'))));
        $fbl_cTotal = (array_sum(array_column($fbl_c, 'debit')))-(array_sum(array_column($fbl_c, 'credit')));
        $fbl_c = array('fbl_cDebit'=>$fbl_cDebit,'fbl_cCredit'=>$fbl_credit,'fbl_cTotal'=>number_format($fbl_cTotal));


        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $expense_1 = \Illuminate\Support\Facades\DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($start_date, $end_date), "and")
            ->where('gl.voucher_status','3')
            ->where('fca_l5.parent_code','like','04-01%')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $expense_2 = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($start_date, $end_date), "and")
            ->where('gl.voucher_status','3')
            ->where('fca_l5.parent_code','like','04-02%')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $income = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date",array($start_date, $end_date), "and")
            ->where('gl.voucher_status','3')
            ->where('fca_l5.parent_code','like','05%')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get()->toArray();


        $income  = (array_sum(array_column($income, 'debit')))-(array_sum(array_column($income, 'credit')));
        $expense_1 = $expense_1->sum('gld.debit')- $expense_1->sum('gld.credit');
        $expense_2= $expense_2->sum('gld.debit')- $expense_2->sum('gld.credit');

        return [
            'trial_balance'=>$trial,
            'js_bank'=>$jsBank,
            'fbl_p'=>$fbl_p,
            'fbl_e'=>$fbl_e,
            'fbl_d'=>$fbl_d,
            'fbl_c'=>$fbl_c,
            'income'=>$income,
            'expense'=>$expense_1+$expense_2

        ];
    }


}
