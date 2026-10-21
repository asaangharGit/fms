<?php


namespace App\Http\Controllers;

use App\Models\Items;
use Illuminate\Http\Request;


class ItemsController extends Controller {



    public function index(){

        $items = Items::whereNull('parent_id')->where('status',1)->get();
        return view('items.index',compact('items'));

    }

    public function create(){

        return view('items.create');

    }

    public function store(Request $req){


        Items::create(['name'=>$req->name,'code'=>$req->code]);
        return redirect()->route('subitems.create')->with('flash_success', 'Item created successfully.');

    }


    public function edit(Request $req){

        $assets = Items::where('id',$req->segment(4))->get()[0];
        return view('items.edit',compact('assets'));

    }

    public function update($id,Request $req){


        Items::where('id',$req->segment(4))->update(['name'=>$req->name,'code'=>$req->code]);
        return redirect()->route('items.all')->with('flash_success', 'Items updated successfully.');

    }

    public function destroy(Request $req){


        Items::where('id',$req->segment(4))->update(['status'=>2]);
        Items::where('parent_id',$req->segment(4))->update(['status'=>2]);
        return redirect()->route('items.all')->with('flash_success', 'Items updated successfully.');

    }


}
