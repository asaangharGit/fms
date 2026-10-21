<?php

namespace App\Http\Controllers;

use App\Models\FinBanksAccount;
use App\Models\FinCheckbook;
use Illuminate\Http\Request;

/**
 * Class FinCheckbookController
 * @package App\Http\Controllers
 */
class FinCheckbookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $finCheckbooks = FinCheckbook::with('banks')->get();


        return view('fin-checkbook.index', compact('finCheckbooks'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $finCheckbook = new FinCheckbook();
        $finBanks = FinBanksAccount::where('status',1)->get();
        return view('fin-checkbook.create', compact('finCheckbook','finBanks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(FinCheckbook::$rules);

        $finCheckbook = FinCheckbook::create($request->all());

        return redirect()->route('fin-checkbooks.index')
            ->with('flash_success', 'FinCheckbook created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $finCheckbook = FinCheckbook::find($id);

        return view('fin-checkbook.show', compact('finCheckbook'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $finCheckbook = FinCheckbook::find($id);
        $finBanks = FinBanksAccount::where('status',1)->get();
        return view('fin-checkbook.edit', compact('finCheckbook','finBanks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  FinCheckbook $finCheckbook
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FinCheckbook $finCheckbook)
    {
        request()->validate(FinCheckbook::$rules);
        $finCheckbook->where('id',$request->segment(4))
            ->update(array('chequebook_name'=>$request->chequebook_name,'checknum_start'=>$request->checknum_start,'checknum_end'=>$request->checknum_end));

        return redirect()->route('fin-checkbooks.index')
            ->with('flash_success', 'FinCheckbook updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $finCheckbook = FinCheckbook::find($id)->delete();

        return redirect()->route('fin-checkbooks.index')
            ->with('flash_success', 'FinCheckbook deleted successfully');
    }
}
