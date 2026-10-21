<?php


namespace App\Http\Controllers;

use App\Models\Items;
use Illuminate\Http\Request;
use DB;

class SubItemsController extends Controller {



    public function index(){

//        SELECT t1.name AS lev1, t2.name as lev2, t3.name as lev3, t4.name as lev4
//FROM category AS t1
//LEFT JOIN category AS t2 ON t2.parent = t1.category_id
//LEFT JOIN category AS t3 ON t3.parent = t2.category_id
//LEFT JOIN category AS t4 ON t4.parent = t3.category_id
//WHERE t1.name = 'ELECTRONICS';

        $assets = DB::table('items as t1')
            ->select('t1.name AS lev1','t2.name as lev2','t2.status','t1.timestamp','t2.id','t2.code')
            ->join('items as t2', 't2.parent_id', '=', 't1.id')
            ->where('t2.status',1)
            ->get();

        return view('subitems.index',compact('assets'));

    }

    public function create(){

        $items = Items::whereNull('parent_id')->get();
        return view('subitems.create',compact('items'));

    }

    public function store(Request $req){

        Items::create(['name'=>$req->sub_item,'parent_id'=>$req->item_id,'code'=>$req->code]);
        return redirect()->route('subitems.all')->with('flash_success', 'Sub Item created successfully.');

    }


    public function edit(Request $req){
        $items = Items::whereNull('parent_id')->get();

        $detail = DB::table('items as t1')
            ->select('t1.name AS lev1','t2.name as lev2','t1.status','t1.timestamp','t2.id','t2.parent_id')
            ->join('items as t2', 't2.parent_id', '=', 't1.id')
            ->where('t2.id',$req->segment(4))
            ->get();



        return view('subitems.edit',compact('detail','items'));

    }

    public function update($id,Request $req){


        Items::where('id',$req->segment(4))->update(['name'=>$req->name,'parent_id'=>$req->item_id]);
        return redirect()->route('subitems.all')->with('flash_success', 'Items updated successfully.');

    }

    public function destroy(Request $req){


        Items::where('id',$req->segment(4))->update(['status'=>2]);
        return redirect()->route('subitems.all')->with('flash_success', 'Subitem deleted successfully.');

    }


    public function getItemCode(Request $request){

      $itemdata = Items::whereNull('parent_id')->where('id',$request->item_id);
       $code = $itemdata->value('code');
       $count = Items::where('parent_id',$itemdata->value('id'))->count();
       echo $code.'-'.++$count;

    }


}
