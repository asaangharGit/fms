<?php


namespace App\Http\Controllers;

use App\Models\FinChartOfAccount;
use App\Models\FinCheckbook;
use App\Models\FinGeneralLedger;
use App\Models\FinVendorInvoice;
use App\Models\FinVendorInvoiceDetail;
use App\Models\FinVendors;
use App\Models\Items;
use Illuminate\Http\Request;
use DB;

class FinVendorsController extends Controller {

    public function index(){

        $vendors = FinVendors::where('status',1)->get();
        return view('fin-vendors.index',compact('vendors'));

    }

    public function create(){

        return view('fin-vendors.create');

    }

    public function store(Request $req){


        FinVendors::create(['name'=>$req->name,'ntn'=>$req->ntn,'contact'=>$req->contact,'address'=>$req->address,'city_id'=>$req->city_id]);
        return redirect()->route('fin-vendors.index')->with('flash_success', 'vendor created successfully.');

    }


    public function edit(Request $req){

        $vendor = FinVendors::where('id',$req->segment(4))->get()[0];
        $accounts = FinChartOfAccount::where('level','L5')->get();
        $cities = DB::Table('cities')->get();
        return view('fin-vendors.edit',compact('vendor','accounts','cities'));

    }

    public function update($id,Request $req){


        FinVendors::where('id',$req->segment(4))->update([
            'name'=>$req->name,
            'ntn'=>$req->ntn,
            'contact'=>$req->contact,
            'address'=>$req->address,
            'city_id'=>$req->city_id,

        ]);
        return redirect()->route('fin-vendors.index')->with('flash_success', 'vendor updated successfully.');

    }

    public function destroy(Request $req){


        FinVendors::where('id',$req->segment(4))->update(['status'=>2]);
        return redirect()->route('fin-vendors.index')->with('flash_success', 'vendor deleted successfully.');

    }



    public function createVendorInvoice(){
        $accounts = FinChartOfAccount::where('level','L5')->get();
        $vendors = FinVendors::where('status',1)->get();
        $binding_acc = ['',374,226];
        return view('fin-vendors.createVendorInvoiceForm',compact('vendors','accounts','binding_acc'));

    }

    public function storeVendorInvoice(Request $req){

        try {
            DB::beginTransaction();

            $data =  [
                'vendor_id' => $req->vendor_id,
                'invoice_no'=>$req->invoice_no,
                'invoice_date'=>$req->invoice_date,
                'due_date'=>$req->due_date,
                'invoice_amount'=>$req->invoice_amount,

            ];
            if($req->vendor_form == 1){

                $FinVendors_id = FinVendors::create([
                    'name'=>$req->name,
                    'ntn'=>$req->ntn,
                    'contact'=>$req->contact,
                    'address'=>$req->address,
                    'city_id'=>$req->city_id
                ])->id;
                $data['vendor_id'] = $FinVendors_id;
            }
            $last_id = FinVendorInvoice::create($data)->id;

            $desc = $req->input('item_desc');
            foreach($desc as $key=>$value):


                FinVendorInvoiceDetail::create(array(
                    'invoice_id'=>$last_id,
                    'coa_id'=>$req->chartofaccount[$key],
                    'item_desc'=>$value,
                    'tax'=>$req->tax[$key],
                    'price'=>$req->price[$key],
                    'total'=>$req->total[$key],


                ));

            endforeach;


            DB::commit();
            }catch(\Exception $e){
            DB::rollback();

            return redirect()->route('fin-vendors.VendorInvoiceList')->with('flash_danger', 'Something went wrong');

        }

        return redirect()->route('fin-vendors.VendorInvoiceList')->with('flash_success', 'Invoice created successfully.');

    }

    public function VendorInvoiceList(){

        $invoice_list = FinVendorInvoice::where('status',1)->get();

        return view('fin-vendors.VendorInvoiceList',compact('invoice_list'));

    }

    public function VendorInvoiceEditForm(Request $req)
    {

        $accounts = FinChartOfAccount::where('level','L5')->get();
        $vendors = FinVendors::where('status',1)->get();
        $data_inv = \App\Models\FinVendorInvoice::where('id',$req->segment(4))->with('invoice_detail')->first();
        return view('fin-vendors.VendorInvoiceEditForm',compact('data_inv','vendors','accounts'));
    }


    public function VendorInvoiceUpdate($id,Request $req){

        try {
            DB::beginTransaction();

            FinVendorInvoice::where('id',$req->segment(4))->update(
                [   'vendor_id'=>$req->vendor_id,
                    'invoice_no'=>$req->invoice_no,
                    'invoice_date'=>$req->invoice_date,
                    'due_date'=>$req->due_date,
                    'invoice_amount'=>$req->invoice_amount,

                ]
            );


            FinVendorInvoiceDetail::where('invoice_id',$req->segment(4))->delete();

            $desc = $req->input('item_desc');

            foreach($desc as $key=>$value):

                FinVendorInvoiceDetail::create(array(
                    'invoice_id'=>$req->segment(4),
                    'coa_id'=>$req->chartofaccount[$key],
                    'item_desc'=>$value,
                    'tax'=>$req->tax[$key],
                    'price'=>$req->price[$key],
                    'total'=>$req->total[$key],
                    'merge'=>$req->merge[$key]

                ));

            endforeach;
            DB::commit();
        }catch(\Exception $e) {
            DB::rollback();

            return redirect()->route('fin-vendors.VendorInvoiceList')->with('flash_danger', 'Something went wrong');
        }

            return redirect()->route('fin-vendors.VendorInvoiceList')->with('flash_success', 'invoice updated successfully.');

    }

    public function VendorInvoiceDestroy(Request $req){


        FinVendorInvoice::where('id',$req->segment(4))->update(['status'=>2]);
        return redirect()->route('fin-vendors.VendorInvoiceList')->with('flash_success', 'invoice deleted successfully.');

    }


    public function getVendorInvoices(Request $req){
        $data ='';
        $data .= '<option>Select</option>';
        $coa_id = FinVendors::where('id',$req->vendor_id)->value('coa_id');
        $invoices = FinVendorInvoice::where('vendor_id',$req->vendor_id)->get();

        foreach ($invoices as $val):
                $total_amount = FinGeneralLedger::where('invoices_id',$val->id)->sum('debit');
                if( $total_amount < $val->invoice_amount){
                    $data .= "<option value=".$val->id.">Invoice no => ".$val->invoice_no." --- Amount => ".$val->invoice_amount."</option>";
                }
        endforeach;

                return array('options'=>$data,'coa_id'=>$coa_id);

    }



    public function getInvoiceAmount(Request $req){

      $invoice_amount = FinVendorInvoice::where('id',$req->segment(4))->get()[0];
      $paid =  FinGeneralLedger::where('invoices_id',$req->segment(4))->sum('debit');

      return  array('diff'=>$invoice_amount->invoice_amount-$paid,'date'=>$invoice_amount->invoice_date);

    }


    public function openVendorForm(){

//        $accounts = FinChartOfAccount::where('level','L5')->get();
        $cities = DB::Table('cities')->get();

        return view('fin-vendors.form',compact('cities'));

    }



    public function approveVendorInvoice(Request $request)
    {

        $TotalDebit = 0;
        $TotalCredit = 0;
        $invoice = FinVendorInvoice::where('id',$request->id)->with('invoice_detail')->first();
        $LastSeries = FinGeneralLedger::where(['txn_type' => 5])->orderBy("id", "desc")->first();
        if (!$LastSeries) {
            $LastSeries = 1;
        } else {
            $LastSeries = $LastSeries->txn_series + 1;
        }
        $Model_GL = \App\Models\FinGeneralLedger::create([
            'user_id' => \Auth::user()->id,
            'details' => 'Opening invoice no '.$invoice->invoice_no.' for '.FinVendors::where('id',$request->vendor_id)->value('name').' '.date('Y-m-d'),
            'debit' => 0,
            'credit' => 0,
            'txn_date' => date("Y-m-d"),
            'txn_type' => 5,
            'txn_series' => $LastSeries,
            'office_id' => 1,
            'vendor_id'=>$request->vendor_id,
            'invoices_id'=>$request->id,
            'voucher_status'=>3
        ]);
        $FinGL_Id = $Model_GL->id;

        foreach ($invoice->invoice_detail as $key => $val) {

            if ($key < 2) {
                $TotalDebit +=  ($val->merge == 1) ? $val->price+$val->tax :$val->price;
                \App\Models\FinGeneralLedgerDetail::create([
                    'fin_gen_id' => $FinGL_Id,
                    'detail' => $val->item_desc,
                    'coa_id' => $val->coa_id,
                    'debit' => ($val->merge == 1) ? $val->price+$val->tax :$val->price,
                    'credit' => 0
                ]);

            }else{
                $TotalCredit +=  ($val->merge == 1) ? $val->price+$val->tax :$val->price;
                \App\Models\FinGeneralLedgerDetail::create([
                    'fin_gen_id' => $FinGL_Id,
                    'detail' => $val->item_desc,
                    'coa_id' => $val->coa_id,
                    'debit' => 0,
                    'credit' =>  ($val->merge == 1) ? $val->price+$val->tax :$val->price,
                ]);

            }


        }
        FinGeneralLedger::find($FinGL_Id)->update(['debit' => $TotalDebit, 'credit' => $TotalCredit]);
        FinVendorInvoice::where('id',$request->id)->update(array('approval_status'=>2));

    }

    public function ViewVendorInvoice(Request $req){

        $accounts = FinChartOfAccount::where('level','L5')->get();
        $vendors = FinVendors::where('status',1)->get();
        $data_inv = \App\Models\FinVendorInvoice::where('id',$req->segment(4))->with(['invoice_detail','vendors'])->first();;
        return view('fin-vendors.ViewVendorInvoice',compact('data_inv','vendors','accounts'));
    }

    public function payments()
    {
        $invoice_list = FinVendorInvoice::where([['status','=',1],['approval_status','2']])->get();
        return view('fin-vendors.payments',compact('invoice_list'));
    }


    public function recievePayment(Request $req)
    {
        $chartOfAccounts = FinChartOfAccount::where("level", "L5")->orderBy("code")->get();
        $banks = FinCheckbook::where('status',1)->get();
        $invoice = FinVendorInvoice::where('id',$req->segment(4))->with('vendors')->first();
        $accrows = [226,227,222,354];
        return view('fin-vendors.recievePayment',compact('banks','chartOfAccounts','invoice','accrows'));

    }

    public function getChequeNum(Request $req)
    {

        $cheque = FinCheckbook::where('id',$req->chequeId)->first();
        $latest = FinGeneralLedger::select('cheque_number')->where('cheque_id',$req->chequeId)->latest('cheque_number')->first();

        if($latest){

            if(($latest->cheque_number+1) > $cheque->checknum_end){
                return 0;
            }
           return $latest->cheque_number+1;
        }else{

            return $cheque->checknum_start;

        }
    }


    public function storeRecievedPayment(Request $request){
        $LastSeries = FinGeneralLedger::where(['txn_type' => 1])->orderBy("id", "desc")->first();
        if (!$LastSeries) {
            $LastSeries = 1;
        } else {
            $LastSeries = $LastSeries->txn_series + 1;
        }

        $Model_GL = \App\Models\FinGeneralLedger::create([
            'user_id' => \Auth::user()->id,
            'details' => $request->purpose,
            'debit' => 0,
            'credit' => 0,
            'txn_date' => $request->TxnDate,
            'txn_type' => 1,
            'txn_series' => $LastSeries,
            'cheque_number' => $request->chqnum,
            'office_id' => 1,
            'vendor_id'=>$request->vendor_id,
            'invoices_id'=>$request->invoice_id,
            'cheque_id' => $request->cheque_id,
            'voucher_status'=>3
        ]);
        $FinGL_Id = $Model_GL->id;

        $TotalDebit = 0;
        $TotalCredit = 0;
        foreach ($request->chartofaccount as $key => $val) {
            if (isset($val)) {
                $TotalDebit += $request->debit[$key];
                $TotalCredit += $request->credit[$key];

                \App\Models\FinGeneralLedgerDetail::create([
                    'fin_gen_id' => $FinGL_Id,
                    'detail' => $request->detail[$key],
                    'coa_id' => $val,
                    'debit' => $request->debit[$key],
                    'credit' => $request->credit[$key]
                ]);
            }
        }
        FinGeneralLedger::find($FinGL_Id)->update(['debit' => $TotalDebit, 'credit' => $TotalCredit]);

        return redirect()->route('fin-vendors.payments')->with('flash_success', 'payment created successfully.');
    }


}
