<?php

namespace App\Http\Controllers;

use App\Models\FinChartOfAccount;
use Illuminate\Http\Request;
use DB;

/**
 * Class FinChartOfAccountController
 * @package App\Http\Controllers
 */
class FinChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $finChartOfAccounts = FinChartOfAccount::orderBy('code')->get();
//                $loanBankslips = DB::table('fin_chart_of_accounts AS t1')
//                    ->select('t1.title AS lev1, t2.title as lev2, t3.title as lev3, t4.title as lev4')
//                    ->leftjoin('fin_chart_of_accounts AS t2', 't2.parent_code', '=', 't1.code')
//                    ->leftjoin('fin_chart_of_accounts AS t3', 't3.parent_code', '=', 't2.code')
//                    ->leftjoin('fin_chart_of_accounts AS t4', 't4.parent_code', '=', 't3.code')
//                    ->get();
//        $finChartOfAccounts = DB::select('SELECT t1.title AS lev1Title, t1.code AS lev1Code,
//        t2.title as lev2Title,t2.code AS lev2Code,
//        t3.title as lev3title,t3.code AS lev3Code,
//        t4.title as lev4Title,t4.code AS lev4Code,
//        t5.title as lev5Title,t5.code AS lev5Code
//        FROM fin_chart_of_accounts AS t1
//        LEFT JOIN fin_chart_of_accounts AS t2 ON t2.parent_code = t1.code
//        LEFT JOIN fin_chart_of_accounts AS t3 ON t3.parent_code = t2.code
//        LEFT JOIN fin_chart_of_accounts AS t4 ON t4.parent_code = t3.code
//        LEFT JOIN fin_chart_of_accounts AS t5 ON t5.parent_code = t4.code');
//                echo "<pre>";
//                print_r($finChartOfAccounts);die;
//
////        die;
//


        return view('fin-chart-of-account.index', compact('finChartOfAccounts'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $finChartOfAccount = new FinChartOfAccount();
        return view('fin-chart-of-account.create', compact('finChartOfAccount'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(FinChartOfAccount::$rules);

        $finChartOfAccount = FinChartOfAccount::create([
            'code'=>$request->code,
            'parent_code'=>$request->l4_code,
            'title'=>$request->title,
            'level'=>'L5'
        ]);

        return redirect()->route('fin-chart-of-accounts.index')
            ->with('flash_success', 'FinChartOfAccount created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $finChartOfAccount = FinChartOfAccount::find($id);

        return view('fin-chart-of-account.show', compact('finChartOfAccount'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $finChartOfAccount = FinChartOfAccount::find($id);

        return view('fin-chart-of-account.edit', compact('finChartOfAccount'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  FinChartOfAccount $finChartOfAccount
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {


        FinChartOfAccount::where('id',$id)->update(['title'=>$request->title]);

        return redirect()->route('fin-chart-of-accounts.index')
            ->with('flash_success', 'FinChartOfAccount updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $finChartOfAccount = FinChartOfAccount::find($id)->delete();

        return redirect()->route('fin-chart-of-accounts.index')
            ->with('flash_success', 'FinChartOfAccount deleted successfully');
    }

    public function getL4Code(Request $request){

        $l4 = FinChartOfAccount::where('parent_code','like',$request->acc_id.'%')->where('level','L4')->get();
        echo '<option value="">Select</option>';
        foreach ($l4 as $value){ ?>

            <option value="<?=$value->code?>"><?=$value->code?> <?=$value->title?></option>

        <?php }

    }
}
