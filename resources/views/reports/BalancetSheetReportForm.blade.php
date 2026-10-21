@extends('layouts.master')
@section('page_title', 'Balance Sheet Report')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
    <?php


    ?>
    <div class="card card-default">
        <div class="card-body">

            <form method="POST" action="{{ route('reports.BalancetSheetReportDetail') }}">
                @csrf

                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Date from</label>
                                <input required name="datefrom" class="form-control <?=($errors->has('datefrom') ? ' is-invalid' : '')?>" type="date" value="{{$datefrom}}" max="{{date("Y-m-d")}}">
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Date To</label>
                                <input required name="dateto" class="form-control <?=($errors->has('dateto') ? ' is-invalid' : '')?>" type="date" value="{{$dateto}}" max="{{date("Y-m-d")}}">
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>U$D Rate</label>
                                <input class="form-control" name="usd_rate" value="{{$usd_rate}}">

                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <br>
                                <button type="submit" class="btn btn-primary"><i class="icon-chevron-right"></i>Generate</button>
                            </div>
                        </div>
                    </div>
                    <br>

                </div>
            </form>

            @if(($datefrom) != '' && ($dateto) != '')

{{--                {{"Prev PL: ".  $p_l}}--}}
{{--                <br>--}}
{{--{{"current PL: ".  ($p_l_current)}}--}}


           <br>
                @php
                      $p_l = $p_l+($p_l_current);

                      $equity_total = abs($equity_1)+($p_l)+$equity_3;
                @endphp

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            {{Qs::reportHeader($datefrom,$dateto)}}
                            <table  id="example" class="table table-bordered datatable-button-html5-columns">

                                <thead>
                                <tr>
                                    <th></th>
                                    <th><h3><strong>EQUITY</strong></h3></th>
                                    <th></th>
                                </tr>
                                <tr class="datatable-header center">
                                    <th><strong>----- (US Dollar) ------</strong></th>
                                    <th></th>
                                    <th><strong>------ (Rupees) ------</strong></th>

                                </tr>
                                </thead>
                                <tbody>
                                <tr class="datatable-header center">
                                    <th id="eq_1">{{number_format(abs($equity_1)/$usd_rate)}}</th>
                                    <th>Issued, Subscribed And Paid-Up Capital</th>
                                    <th>{{number_format(abs($equity_1))}}</th>

                                </tr>

                                <tr class="datatable-header center">
                                    <th id="eq_2">{{number_format(abs($p_l)/$usd_rate)}}</th>
                                    <th>Retained earnings</th>
                                    <th>
                                        @if($p_l < 0)
                                            ({{number_format(abs($p_l))}} )
                                        @else
                                            {{number_format(abs($p_l))}}
                                        @endif


                                    </th>

                                </tr>
                                <tr class="datatable-header center">
                                    <th id="eq_2">{{number_format(abs($equity_3)/$usd_rate)}}</th>
                                    <th>Revaluation Reserves</th>
                                    <th>{{number_format(abs($equity_3))}}</th>

                                </tr>

                                <tr>
                                    <th id="total_equity">{{number_format(abs($equity_total)/$usd_rate)}}</th>
                                    <th class="text-right"><strong>Total Equity</strong></th>
                                    <th><strong>{{number_format(abs($equity_total))}}</strong></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>
                                        <h3>
                                            <strong>LIABILITIES</strong>
                                        </h3>
                                    </th>
                                    <th></th>
                                </tr>
                                <tr class="datatable-header center">
                                    <th></th>
                                    <th><h3><strong>Non-current liabilities</strong></h3></th>
                                    <th></th>

                                </tr>
                                @php
                                    $liabality_total = 0;
                                    $liabality_total_2 = 0;
                                    $current_lib_value= 0;
                                @endphp

                                @foreach($non_current_liabilities as $val2)
                                    @php
                                         $liabality_total += abs($val2->debit-$val2->credit);
                                    @endphp
                                    <tr>
                                        <th>{{number_format(abs($val2->debit-$val2->credit)/$usd_rate)}}</th>
                                        <th>{{$val2->L5_Title }}</th>
                                        <th>{{number_format(abs($val2->debit-$val2->credit))}}</th>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>

                                </tr>
                                <tr>
                                    <th></th>
                                    <th><h3><strong>Current liabilities</strong></h3></th>
                                    <th></th>

                                </tr>



                                @foreach($current_lib_acc as $val3)
                                    @php

                                        $lib_accounts = \App\Models\FinChartOfAccount::select('id')->where('level','L5')->where('parent_code','like',$val3->code.'%')->get()->toArray();

                                        $current_liabilities = DB::table("fin_general_ledgers as gl")
                                        ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
                                        ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
                                        ->whereDate("gl.txn_date", '<=', $dateto)
                                        ->whereIn('gld.coa_id',$lib_accounts)
                                        ->select(DB::raw("
                                        fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                                        gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
                                        ->groupBy('fca_l5.code')
                                        ->get();


                                     @endphp



                                    @if(!empty($current_liabilities->toArray()))

                                    @foreach ($current_liabilities as $val_lib)

                                        @php
                                              $liabality_total_2 += abs($val_lib->debit-$val_lib->credit);
                                              $current_lib_value +=  abs($val_lib->debit-$val_lib->credit)

                                        @endphp
                                    @endforeach
                                    <tr>
                                        <th>{{number_format(abs($current_lib_value)/$usd_rate)}} </th>

                                        <th>{{$val3->title}}</th>
                                        <th>{{number_format(abs($current_lib_value))}}
                                        @php $current_lib_value=0; @endphp
                                        </th>
                                    </tr>
                                    @endif


                                @endforeach


                                <tr>
                                    <th id="total_liability">{{number_format(abs($liabality_total+$liabality_total_2)/$usd_rate)}}</th>
                                    <th class="text-right"><strong>Total liabilities</strong></th>
                                    <th><strong>{{number_format(abs($liabality_total+$liabality_total_2))}}</strong></th>
                                </tr>
                                <tr>
                                    <th id="total_liability_equity">{{number_format(abs($liabality_total_2+$liabality_total+$equity_total)/$usd_rate)}}</th>
                                    <th><h3><strong>Total equity and liabilities</strong></h3></th>
                                    <th><h3><strong>{{number_format(abs($liabality_total_2+$liabality_total+$equity_total))}}</strong></h3></th>

                                </tr>
                                <tr>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>

                                </tr>


                                <tr>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>

                                </tr>

                                <tr>
                                    <th id="prof_ater"></th>
                                    <th><h3><strong>ASSETS</strong></h3></th>
                                    <th></th>

                                </tr>
                                <tr>
                                    <th id="prof_ater"></th>
                                    <th><strong>Non-current assets</strong></th>
                                    <th></th>

                                </tr>

                                    @php
                                    $non_current_assets_total = 0;
                                     $non_current_assets_value = 0;

                                     $tangible = abs($non_current_asset_1->sum('gld.debit')-$non_current_asset_1->sum('gld.credit'));
                                     $depreciation  =  abs($non_current_asset_2->sum('gld.debit')-$non_current_asset_2->sum('gld.credit'));

                                     $tangible_final = abs($tangible-$depreciation);

                                    @endphp
                                    <tr>
                                        <th>{{number_format(abs($tangible_final)/$usd_rate)}} </th>

                                        <th>Tangible Assets</th>
                                        <th>{{number_format($tangible_final)}} </th>
                                    </tr>
                                        @foreach($non_current_asset_acc as $val4)
                                            @php

                                                $non_current_asset_accounts = \App\Models\FinChartOfAccount::select('id')->where('level','L5')
                                                ->whereNotIn('parent_code',array('01-01-001-001','01-01-001-002'))
                                                ->where('parent_code','like',$val4->code.'%')
                                                ->get()->toArray();

                                                $non_current_asset_adetail = DB::table("fin_general_ledgers as gl")
                                                ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
                                                ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
                                                ->whereDate("gl.txn_date", '<=', $dateto)
                                                ->whereIn('gld.coa_id',$non_current_asset_accounts)

                                                ->select(DB::raw("
                                                fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                                                gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
                                                ->groupBy('fca_l5.code')
                                                ->get();



                                           @endphp

                                    @if(!empty($non_current_asset_adetail->toArray()))

                                        @foreach ($non_current_asset_adetail as $val_nca)
                                            @php
                                                   $non_current_assets_total += abs($val_nca->debit-$val_nca->credit);
                                                   $non_current_assets_value +=abs($val_nca->debit-$val_nca->credit);
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <th> </th>

                                            <th>{{$val4->title}} {{$val4->code }}</th>
                                            <th>{{number_format(abs($non_current_assets_value))}}
                                            @php
                                                $non_current_assets_value = 0;

                                            @endphp
                                            </th>
                                        </tr>


                                    @endif


                                @endforeach


                                <tr>
                                    <th id="total_non_current">{{number_format(abs($non_current_assets_total+$tangible_final)/$usd_rate)}}</th>
                                    <th class="text-right"><strong>Total asset</strong></th>
                                    <th><strong>{{number_format(abs($non_current_assets_total+$tangible_final))}}</strong></th>

                                </tr>
                                <tr>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>

                                </tr>

                                <tr>
                                    <th id="prof_ater"></th>
                                    <th><strong>Current assets</strong></th>
                                    <th></th>


                                </tr>
                                @php
                                    $current_assets_total = 0;
                                     $current_assets_value = 0;
                                @endphp
                                @foreach($current_asset_acc as $val5)
                                    @php

                                        $current_asset_accounts = \App\Models\FinChartOfAccount::select('id')->where('level','L5')->where('parent_code','like',$val5->code.'%')->get()->toArray();

                                        $current_asset_adetail = DB::table("fin_general_ledgers as gl")
                                        ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
                                        ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
                                        ->whereDate("gl.txn_date", '<=', $dateto)
                                        ->whereIn('gld.coa_id',$current_asset_accounts)
                                        ->select(DB::raw("
                                        fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                                        gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
                                        ->groupBy('fca_l5.code')
                                        ->get();



                                    @endphp

                                    @if(!empty($current_asset_adetail->toArray()))

                                        @foreach ($current_asset_adetail as $val_ca)

                                            @php

                                                $current_assets_total +=($val_ca->debit-$val_ca->credit);
                                                $current_assets_value += abs($val_ca->debit-$val_ca->credit);

                                            @endphp
                                        @endforeach
                                        <tr>
                                            <th>{{number_format(abs($current_assets_value)/$usd_rate)}} </th>
                                            <th>{{$val5->title}}</th>
                                            <th>{{number_format($current_assets_value)}}
                                            @php
                                                $current_assets_value= 0;
                                            @endphp
                                            </th>
                                        </tr>


                                    @endif


                                @endforeach
                                <tr>
                                    <th id="total_current_asset">{{number_format(abs($current_assets_total)/$usd_rate)}}</th>
                                    <th class="text-right"><strong>Total Current asset</strong></th>
                                    <th><strong>{{number_format(abs($current_assets_total))}}</strong></th>

                                </tr>
                                <tr>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>
                                    <th style="height: 50px;border:none"></th>

                                </tr>

                                <tr>
                                    <th id="total_asset">{{number_format(abs($current_assets_total+$non_current_assets_total+$tangible_final)/$usd_rate)}}</th>
                                    <th><h3><strong>Total assets</strong></h3></th>
                                    <th><h3><strong>{{number_format(abs($current_assets_total+$non_current_assets_total+$tangible_final))}}</strong></h3></th>

                                </tr>


                                </tbody>

                            </table><br><br>
                        </div>
                    </div>


                </div>

        </div>
    </div>
    @endif

@endsection
