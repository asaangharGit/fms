<?php


namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Models\Items;
use App\Models\LeaseObligationDetail;
use App\Models\LeaseObligationYears;
use Illuminate\Http\Request;
use DB;
use App\Models\LeaseObligations;

class LeaseObligationController extends Controller {



    public function index(){

//echo "<pre>";
//        $lease  = LeaseObligations::where('status',1)->get();
//        foreach ($lease as $val):
//
//
//                $month = $val->value('months');
//                 for ($x = 1; $x <= 24; $x++) {
//                $detail = LeaseObligationDetail::where('lease_id',$val->id)->orderBy('id','desc')->offset(0)->limit(1);
//                foreach ($detail->get() as $val2):
//
//                       $final = date("Y-m-d", strtotime("+".$x. "month", strtotime($val->commencement_date)));
//                       $counter = $detail->count();
//                       $difference = Qs::monthDifference($final,$val->commencement_date);
//                       $percentage = 0;
//                       $peryearraise = 0;
//                       echo $difference;
//                       echo "<br>";
//                       $last_rent = LeaseObligationDetail::where('lease_id',$val->id)->offset(0)->limit(1)->value('total_payments');
//
//                    if($difference > 12 && $difference <= 24){
//                           $percentage = $val->annual_increase;
//                            $peryearraise = ($percentage / 100) * $last_rent;
//
//                       }
//                       else if($difference >= 25 && $difference <= 36){
//
//echo "asdasda";
//
//                           $percentage = $val->annual_increase;
//                           $peryearraise = ($percentage / 100) * $last_rent;
//                       }
//
//                       else if($difference >= 37 && $difference <= 48){
//                           $percentage = $val->annual_increase;
//                           $peryearraise = ($percentage / 100) * $val2->total_payments;
//                       }
//                       else if($difference >= 49 && $difference <= 60){
//                           $percentage = $val->annual_increase;
//                           $peryearraise = ($percentage / 100) * $val2->total_payments;
//                       }
//
//                        $totalpayment = $val->rent+$peryearraise;
//                           $pvf = pow(1/((1+(1.01/100))),$counter );
//                      //  $pvf  = sprintf('%.4f', floor($pvf_calculate*10000*($pvf_calculate>0?1:-1))/10000*($pvf_calculate>0?1:-1));
//                        $fin_lease_cost = $val2->remaining_liabality*(1.03/100);
//                        $pr_payment = $totalpayment-$fin_lease_cost;
//                        $liability = $val2->remaining_liabality-$pr_payment;
//                        $data = [
//                            'lease_id'=>$val2->lease_id,
//                            'year'=>$val2->year,
//                            'total_payments'=>$totalpayment,
//                            'pvf'=>$pvf,
//                            'present_value'=>$pvf*$totalpayment,
//                            'liabality'=>$val2->remaining_liabality,
//                            'fin_lease_cost'=>$fin_lease_cost,
//                            'principal_repayment'=>$pr_payment,
//                            'remaining_liabality'=>$liability,
//                            'payment_date'=>$final,
//
//
//
//                        ];
//
//                        print_r($data);
//                    LeaseObligationDetail::create($data);
//                endforeach;
//                       }
//
//
//        endforeach;
//die;
        $obligations = LeaseObligations::where('status',1)->get();
        return view('lease-obligation.index',compact('obligations'));

    }

    public function create(){

        return view('lease-obligation.create');

    }

    public function store(Request $req){

        LeaseObligations::truncate();
        LeaseObligationYears::truncate();
        LeaseObligationDetail::truncate();
       $leaseObl =  LeaseObligations::create([
            'name'=>$req->name,
            'annual_increase'=>$req->annual_increase,
            'interest'=>$req->interest,
            'irr'=>$req->irr,
            'opening_balance'=>$req->opening_balance,

        ]);

        $counter= 1;
        foreach ($req->commencement_date as $key=>$val):

             LeaseObligationYears::create([
                 'lease_id'=>$leaseObl->id,
                 'commencement_date'=>$val,
                 'months'=>$req->months[$key],
                 'expire_date'=>$req->expire_date[$key],
                 'rent'=>$req->rent[$key]
             ]);



            foreach(Qs::DateDifference($val,$req->expire_date[$key]) as $key2=>$val){

                if($val->format("d") > 1){continue;}

                if($counter == 1){
                     LeaseObligationDetail::create([
                    'lease_id'=>$leaseObl->id,
                    'year'=>Qs::getCurrentSession(),
                    'total_payments'=>$req->rent[$key],
                    'pvf'=>1.0000,
                    'present_value'=>$req->rent[$key],
                    'liabality'=>$req->opening_balance,
                    'fin_lease_cost'=>0,
                    'principal_repayment'=>$req->rent[$key],
                    'remaining_liabality'=>$req->opening_balance-$req->rent[$key],
                    'payment_date'=>$val->format("Y-m-d")]);
                     $counter++;
                }else{


                    $detail = LeaseObligationDetail::where('lease_id',$leaseObl->id)->orderBy('id','desc')->offset(0)->limit(1);
                    $pvf = pow(1/((1+($req->interest/100))),$detail->count() );
                    $fin_lease_cost = $detail->value('remaining_liabality')*($req->irr/100);
                    $pr_payment = $detail->value('total_payments')-$fin_lease_cost;
                    $liability = $detail->value('remaining_liabality')-$pr_payment;
                    $data = [
                        'lease_id'=>$leaseObl->id,
                        'year'=>Qs::getCurrentSession(),
                        'total_payments'=>$req->rent[$key],
                        'pvf'=>$pvf,
                        'present_value'=>$pvf*$req->rent[$key],
                        'liabality'=>$detail->value('remaining_liabality'),
                        'fin_lease_cost'=>$fin_lease_cost,
                        'principal_repayment'=>$pr_payment,
                        'remaining_liabality'=>$liability,
                        'payment_date'=>$val->format("Y-m-d"),

                    ];

                    LeaseObligationDetail::create($data);

                }


            }


        endforeach;

        return redirect()->route('lease-obligations.all')->with('flash_success', 'Lease created successfully.');


    }


    public function edit(Request $req){


        $detail = LeaseObligations::where('id',$req->segment(3))->get();
        return view('lease-obligation.edit',compact('detail'));

    }

    public function update($id,Request $req){


        LeaseObligations::where('id',$req->segment(3))->update([
            'name'=>$req->name,
            'rent'=>$req->rent,
            'years'=>$req->years,
            'annual_increase'=>$req->annual_increase,
            'interest'=>$req->interest,
            'irr'=>$req->irr,
            'opening_balance'=>$req->opening_balance,
            'commencement_date'=>$req->commencement_date,
            'expire_date'=>$req->expire_date,
            'opening_balance'=>$req->opening_balance
        ]);

        return redirect()->route('lease-obligations.all')->with('flash_success', 'Lease Obligation updated successfully.');

    }

    public function destroy(Request $req){


        LeaseObligations::where('id',$req->segment(3))->update(['status'=>2]);
        return redirect()->route('lease-obligations.all')->with('flash_success', 'Asset Registration deleted successfully.');

    }


//    public function renewSession(){
//
//
//        $detail = DB::table('asset_depreciation')
//            ->join('items', 'items.id', '=', 'asset_depreciation.item_id')
//            ->join('asset_registration', 'items.id', '=', 'asset_registration.item_id')
//            ->where('asset_registration.active',1)
//            ->where('asset_depreciation.year', Qs::getCurrentSession())
//            ->select(  DB::raw('SUM(asset_depreciation.jan+asset_depreciation.feb+asset_depreciation.mar+asset_depreciation.apr
//            +asset_depreciation.may+asset_depreciation.june+asset_depreciation.july+asset_depreciation.aug
//            +asset_depreciation.sept+asset_depreciation.oct+asset_depreciation.nov+asset_depreciation.dec) AS product_count'),'asset_depreciation.item_id','items.name','asset_registration.amount','asset_registration.percent')
//            ->groupBy('asset_depreciation.item_id')
//            ->get();
//
////        echo "<pre>";
////        print_r($detail);die;
//        return view('lease-obligation.renewSession',compact('detail'));
//
//
//    }
//
//
//    public function renewSessionDetail(Request $req){
//
//
//        LeaseObligations::where('id','>',0)->update(['active'=>2]);
//        foreach ($req->item_id as $key => $value):
//           $last_id = LeaseObligations::create(['item_id'=>$value,'active'=>1,'amount'=>$req->remaining_amount[$key],'percent'=>$req->percent[$key],'created_on'=>date('Y-m-d')]);
//           LeaseObligations::where('id',$last_id)->update(['active'=>1]);
//        endforeach;
//
//        return redirect()->route('lease-obligation.all')->with('flash_success', 'Sub Item created successfully.');
//
//
//    }
//
//
//    public function decreaseAssetAmountForm(Request $req){
//
//        $detail = DB::table('asset_registration')
//            ->join('items', 'items.id', '=', 'asset_registration.item_id')
//            ->where('asset_registration.id',$req->segment(4))
//            ->select('asset_registration.id','items.name','items.code')
//            ->get();
//
//         $list = AssetImpairment::where('asset_register_id',$req->segment(4))->get();
//
//        return view('lease-obligation.decreaseAssetAmount',compact('detail','list'));
//
//    }
//
//
//    public function assetImpairmentDetail(Request $req){
//
//        AssetImpairment::create(['asset_register_id'=> $req->segment(4),'amount'=>$req->amount,'type'=>'-']);
//        return redirect()->route('lease-obligation.all')->with('flash_success', 'Impairment created successfully.');
//
//    }
//

}
