@extends('layouts.master')
@section('page_title', 'Balance Sheet Report')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
    @php
        $expense_total =0;
        $revenue_total = 0;
    @endphp
    <div class="card card-default">
        <div class="card-body">

<?php


?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered datatable-button-html5-columns">
                                <thead style="background-color: #97D35D;">
                                <tr class="datatable-header center">
                                    <th>Title</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total_asset = 0;
                                    $total_liability = 0;
                                    $total_equity = 0;
                                @endphp
                                @foreach($assetAccounts as $val)
                                    @php

                                   $asset = DB::table("fin_general_ledgers")
                                            ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
                                            ->where('fin_general_ledger_details.credit','>',0)->where('fin_general_ledger_details.coa_id',$val->id);

                                    @endphp
                                    @if($asset->count() > 0)
                                        @foreach($asset->get() as $val2)
                                            @php  $total_asset += $val2->credit; @endphp
                                            <tr>
                                                <td>{{$val->code}} - {{$val->title}}</td>
                                                <td>{{number_format($val2->credit)}}</td>

                                             </tr>
                                        @endforeach
                                    @endif

                                    @endforeach
                                <tr>
                                    <th class="text-right">Total Asset</th>
                                    <th>{{number_format($total_asset)}}</th>
                                </tr>


                                </tbody>
                                <tbody>

                                @foreach($liabilityAccounts as $val3)
                                    @php

                                        $liablity = DB::table("fin_general_ledgers")
                                                 ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
                                                 ->where('fin_general_ledger_details.debit','>',0)->where('fin_general_ledger_details.coa_id',$val3->id);

                                    @endphp
                                    @if($liablity->count() > 0)
                                        @foreach($liablity->get() as $val4)
                                            @php  $total_liability += $val4->debit; @endphp
                                            <tr>
                                                <td>{{$val3->code}} - {{$val3->title}}</td>
                                                <td>{{number_format($val4->debit)}}</td>

                                            </tr>
                                        @endforeach
                                    @endif

                                @endforeach
                                <tr>
                                    <th class="text-right">Total Liabality</th>
                                    <th>{{number_format($total_liability)}}</th>
                                </tr>


                                </tbody>

                                <tbody>

                                @foreach($equityAccounts as $val5)
                                    @php

                                        $equity = DB::table("fin_general_ledgers")
                                                 ->join("fin_general_ledger_details", "fin_general_ledger_details.fin_gen_id", "=", "fin_general_ledgers.id")
                                                 ->where('fin_general_ledger_details.credit','>',0)->where('fin_general_ledger_details.coa_id',$val5->id);

                                    @endphp
                                    @if($equity->count() > 0)
                                        @foreach($equity->get() as $val6)
                                            @php  $total_equity += $val6->credit; @endphp
                                            <tr>
                                                <td>{{$val5->code}} - {{$val5->title}}</td>
                                                <td>{{number_format($val6->credit)}}</td>

                                            </tr>
                                        @endforeach
                                    @endif

                                @endforeach
                                <tr>
                                    <th class="text-right">Total Equity</th>
                                    <th>{{number_format($total_equity)}}</th>
                                </tr>
                                <tr>
                                    <th colspan="2">&nbsp;</th>
                                </tr>

                                </tbody>

                                <tbody>
                                    <tr>
                                        <th class="text-right">Asset</th>
                                        <th>{{number_format($total_asset)}}</th>
                                    </tr>
                                    <tr>
                                        <th class="text-right">Liabality + Equity</th>
                                        <th>{{number_format($total_liability+$total_equity)}}</th>
                                    </tr>


                                </tbody>

                            </table>
                            <br><br>
                        </div>
                    </div>


                </div>
        </div>

@endsection
