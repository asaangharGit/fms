<?php
namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use DB;

class AccountsRecievableController extends Controller {


    public function installmentList(){

        $loanlist = \App\Models\LoanList::with('installments')->orderBy('id','desc')->get();
        return view('fin-recievable.installmentList',compact('loanlist'));

    }

    public function viewInstallmentDetail(Request $request){

        $data = \App\Models\RecievableAmount::with('loan')->Where([['loan_id','=',$request->loan_id],['approved_by','=',0]])->get();
        return view('fin-recievable.viewInstallmentDetail',compact('data'));


    }


    public function createRecievableJv(Request $request){

            $installment = \App\Models\RecievableAmount::Where('id',$request->installment_id)->with('loan')->first();
            $jv = array();

            if($installment->total_amount > 0){

                $jv['357'] = array(
                    'debit'=>$installment->total_amount,
                    'credit'=>0,
                    'desc'=>'total amount',
                    'account'=> \App\Models\FinChartOfAccount::select('id','code','title')->where("id", 357)->first()
                );

            }
            if($installment->principal > 0){

                $jv['332'] = array(
                    'debit'=>0,
                    'credit'=>$installment->principal,
                    'desc'=>' Profit On Lendings To Individuals',
                    'account'=> \App\Models\FinChartOfAccount::select('id','code','title')->where("id", 332)->first()
                );
            }

            if($installment->profit > 0){

                $jv['148'] = array(
                    'debit'=>0,
                    'credit'=>$installment->profit,
                    'desc'=>' Lendings To Individuals',
                    'account'=> \App\Models\FinChartOfAccount::select('id','code','title')->where("id", 148)->first()
                );

            }

//            if($installment->charity > 0){
//
//                $jv['130'] = array(
//                    'debit'=>0,
//                    'credit'=>$installment->charity,
//                    'desc'=>'this is charity',
//                    'account'=> \App\Models\FinChartOfAccount::select('id','code','title')->where("id", 130)->first()
//                );
//
//            }
//
//            if($installment->penality > 0){
//
//                $jv['131'] =array(
//                    'debit'=>0,
//                    'credit'=>$installment->penality,
//                    'desc'=>'this is penality',
//                    'account'=> \App\Models\FinChartOfAccount::select('id','code','title')->where("id", 131)->first()
//                );
//
//            }


            return view('fin-recievable.createRecievableJv', compact( 'installment','jv'));


    }


    public function storeRecievableJv(Request $request){


        $TotalDebit = 0;
        $TotalCredit = 0;
        $LastSeries = \App\Models\FinGeneralLedger::where(['txn_type' => 5])->orderBy("id", "desc")->first();
        if (!$LastSeries) {
            $LastSeries = 1;
        } else {
            $LastSeries = $LastSeries->txn_series + 1;
        }
        $Model_GL =  \App\Models\FinGeneralLedger::create([
            'user_id' => \Auth::user()->id,
            'details' => $request->purpose,
            'debit' => 0,
            'credit' => 0,
            'txn_date' => $request->TxnDate,
            'txn_type' => 5,
            'txn_series' => $LastSeries,
            'office_id' => 1,
            'voucher_status'=>3
        ]);
        $FinGL_Id = $Model_GL->id;
        foreach ($request->chartofaccount as $key => $val) {

                $TotalDebit += str_replace(",", "", $request->debit[$key]);
                $TotalCredit += str_replace(",", "", $request->credit[$key]);

                \App\Models\FinGeneralLedgerDetail::create([
                    'fin_gen_id' => $FinGL_Id,
                    'detail' => $request->detail[$key],
                    'coa_id' => $val,
                    'debit' => str_replace(",", "", $request->debit[$key]),
                    'credit' => str_replace(",", "", $request->credit[$key])
                ]);

        }
        \App\Models\FinGeneralLedger::find($FinGL_Id)->update(['debit' => $TotalDebit, 'credit' => $TotalCredit]);

        \App\Models\RecievableAmount::where([['loan_id','=',$request->loan_id],['id','=',$request->installment_id]])
                                   ->update(array('approved_by'=>\Auth::user()->id));
        return redirect()->route('fin-recievable.installmentList')->with('flash_success', 'Entry Updated Successfully.');


    }


    public function viewLoanStatement(Request $request){

         return view('fin-recievable.viewLoanStatement');

    }

    public function viewLoanStatementDetail(Request $request){

        $prev = \App\Models\LoanList::with(['installments'=>function($query)use($request){
            $query->whereDate('recovered_date','<',$request->from_date);
            $query->where('approved_by','>',0);
        }])->Where([['id','=',$request->loan_id]])->first();
        $data = \App\Models\LoanList::with(['installments'=>function($query)use($request){
            $query->whereBetween('recovered_date',[$request->from_date,$request->till_date]);
            $query->where('approved_by','>',0);
        }])->Where([['id','=',$request->loan_id]])->first();
        return view('fin-recievable.viewLoanStatementDetail',compact('data','prev','request'));

    }
   public function viewApprovedInstallments(Request $request){

        $data = \App\Models\RecievableAmount::with('loan')->Where([['loan_id','=',$request->loan_id],['approved_by','!=',0]])->get();
        return view('fin-recievable.viewApprovedInstallments',compact('data'));


    }


}

