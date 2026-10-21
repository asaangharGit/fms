<?php


namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Models\AssetImpairment;
use App\Models\FinGeneralLedger;
use App\Models\Items;
use Illuminate\Http\Request;
use DB;
use App\Models\AssetRegistration;

class AssetRegistrationController extends Controller {



    public function index(){


        $registrations = \App\Models\AssetRegistration::with(['item'])->where('status',1)->orderBy('item_id')->get();

        return view('asset-registration.index',compact('registrations'));

    }

    public function create(){
        $bpv = FinGeneralLedger::where('txn_type',1)->get();
        $items = Items::whereNotNull('parent_id')->where('status',1)->get();
        return view('asset-registration.create',compact('items','bpv'));

    }

    public function store(Request $req){

        $exits =  \App\Models\AssetRegistration::where('item_id',$req->item_id)->count();

        if($exits == 0):
            \App\Models\AssetRegistration::create(['item_id'=>$req->item_id,'fin_gen_id'=>$req->fin_gen_id,'active'=>1,'amount'=>$req->amount,'percent'=>$req->percent,'created_on'=>$req->created_on]);
            return redirect()->route('asset-registration.all')->with('flash_success', 'Sub Item created successfully.');
        else:
            return redirect()->route('asset-registration.create')->with('flash_danger', 'Duplicate Entry.');
        endif;


    }


    public function edit(Request $req){
        $bpv = FinGeneralLedger::where('txn_type',1)->get();
        $items = Items::whereNotNull('parent_id')->where('status',1)->get();
        $detail = \App\Models\AssetRegistration::where('id',$req->segment(4))->get();

        return view('asset-registration.edit',compact('detail','items','bpv'));

    }

    public function update($id,Request $req){


        \App\Models\AssetRegistration::where('id',$req->segment(4))->update(['created_on'=>$req->created_on,'item_id'=>$req->item_id,'fin_gen_id'=>$req->fin_gen_id,'amount'=>$req->amount,'percent'=>$req->percent]);
        return redirect()->route('asset-registration.all')->with('flash_success', 'Asset Registration updated successfully.');

    }

    public function destroy(Request $req){


        \App\Models\AssetRegistration::where('id',$req->segment(4))->update(['status'=>2]);
        return redirect()->route('asset-registration.all')->with('flash_success', 'Asset Registration deleted successfully.');

    }


    public function renewSession(){


        $detail = DB::table('asset_depreciation')
            ->join('items', 'items.id', '=', 'asset_depreciation.item_id')
            ->join('asset_registration', 'items.id', '=', 'asset_registration.item_id')
            ->where('asset_registration.active',1)
            ->where('asset_depreciation.year', Qs::getCurrentSession())
            ->select(  DB::raw('SUM(asset_depreciation.jan+asset_depreciation.feb+asset_depreciation.mar+asset_depreciation.apr
            +asset_depreciation.may+asset_depreciation.june+asset_depreciation.july+asset_depreciation.aug
            +asset_depreciation.sept+asset_depreciation.oct+asset_depreciation.nov+asset_depreciation.dec) AS product_count'),'asset_depreciation.item_id','items.name','asset_registration.amount','asset_registration.percent')
            ->groupBy('asset_depreciation.item_id')
            ->get();

//        echo "<pre>";
//        print_r($detail);die;
        return view('asset-registration.renewSession',compact('detail'));


    }


    public function renewSessionDetail(Request $req){


        AssetRegistration::where('id','>',0)->update(['active'=>2]);
        foreach ($req->item_id as $key => $value):
           $last_id = AssetRegistration::create(['item_id'=>$value,'active'=>1,'amount'=>$req->remaining_amount[$key],'percent'=>$req->percent[$key],'created_on'=>date('Y-m-d')]);
           AssetRegistration::where('id',$last_id)->update(['active'=>1]);
        endforeach;

        return redirect()->route('asset-registration.all')->with('flash_success', 'Sub Item created successfully.');


    }


    public function decreaseAssetAmountForm(Request $req){

        $detail = DB::table('asset_registration')
            ->join('items', 'items.id', '=', 'asset_registration.item_id')
            ->where('asset_registration.id',$req->segment(4))
            ->select('asset_registration.id','items.name','items.code')
            ->get();

         $list = AssetImpairment::where('asset_register_id',$req->segment(4))->get();

        return view('asset-registration.decreaseAssetAmount',compact('detail','list'));

    }


    public function assetImpairmentDetail(Request $req){

        AssetImpairment::create(['asset_register_id'=> $req->segment(4),'amount'=>$req->amount,'type'=>'-']);
        return redirect()->route('asset-registration.all')->with('flash_success', 'Impairment created successfully.');

    }


}
