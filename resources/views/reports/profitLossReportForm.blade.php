@extends('layouts.master')
@section('page_title', 'Profit & Loss Report')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>

    <div class="card card-default">
        <div class="card-body">

            <form method="POST" action="{{ route('reports.profitLossReportDetail') }}">
                @csrf

                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="row">
{{--                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">--}}
{{--                                <label style="font-size: 16px">Show Monthwise &nbsp;&nbsp;<input type="checkbox" id="myCheck" onclick="myFunction()" value="yes" name="monthly"></label>--}}
{{--                            </div>--}}

                        </div>
                        <div class="row" id="date_filter">

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <label>Date from</label>
                                <input required name="datefrom" class="form-control <?=($errors->has('datefrom') ? ' is-invalid' : '')?>" type="date" value="{{$datefrom}}" max="{{date("Y-m-d")}}">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <label>Date To</label>
                                <input required name="dateto" class="form-control <?=($errors->has('dateto') ? ' is-invalid' : '')?>" type="date" value="{{$dateto}}" max="{{date("Y-m-d")}}">
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                <br><br>
                                <button type="submit" class="btn btn-primary"><i class="icon-chevron-right"></i>Generate</button>
                            </div>
                        </div>
                    </div>
                    <br>

                </div>
            </form>
            @if(($datefrom) != '' && ($dateto) != '')
                @php

                   $revenue_total=0;
                   $expense_total=0;

                @endphp
                <div class="row">

                    <div class="col-md-8 text-right"></div>
                    <div class="col-md-2">
                        <strong>Example : 1 PKR = 0.0044 USD</strong>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" id="rate" onkeyup="pkrToUsd()">
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            {{Qs::reportHeader($datefrom,$dateto)}}
                            <table class="table table-bordered datatable-button-html5-columns">

                                <thead>
                                <tr>
                                    <th></th>
                                    <th><h3><strong>INCOME</strong></h3></th>
                                    <th></th>
                                </tr>
                                <tr class="datatable-header center">
                                    <th><strong>----- (US Dollar) ------</strong></th>
                                    <th></th>
                                    <th><strong>------ (Rupees) ------</strong></th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($income as $val3)
                                    @php
                                        $revenue_total+=($val3->debit-$val3->credit);
                                    @endphp
                                    <tr>
                                        <th></th>
                                        <th>{{$val3->L5_Title }}  </th>
                                        <th>
                                            @if(($val3->debit-$val3->credit) < 0)
                                                ({{number_format(abs($val3->debit-$val3->credit)) }})
                                            @else
                                                {{number_format(abs($val3->debit-$val3->credit)) }}
                                            @endif
                                        </th>
                                    </tr>
                                    @endforeach
                                <tr>
                                    <th></th>
                                    <th class="text-right"><strong>Total</strong></th>
                                    <th><strong>{{number_format(abs($revenue_total))}}</strong></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>
                                        <h3>
                                            <strong>EXPENSE</strong>
                                        </h3>
                                    </th>
                                    <th></th>
                                </tr>
                                    <tr>
                                        <th></th>
                                        <th> Operating Expenses</th>
                                        <th>{{$expense_1}}</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th> Administrative Expenses</th>
                                        <th>{{number_format($expense_2-$ProvisionForIncomeTax)}}</th>
                                    </tr>



                                <tr>
                                    <th></th>
                                    <th class="text-right"><strong>Total</strong></th>
                                    <th><strong>{{number_format(($expense_2- $ProvisionForIncomeTax)+$expense_1)}}</strong></th>
                                </tr>




                                @php
                                    $expense_total = ($expense_2- $ProvisionForIncomeTax)+$expense_1;
                                @endphp
                                <tr>
                                    <th id="prof_ater"></th>
                                    <th><h3><strong>Total </strong></h3></th>{{$expense_total}}
                                    <th><strong>{{number_format((-$revenue_total-$expense_total))}}</strong></th>

                                </tr>
                                <tr>
                                    <th></th>
                                    <th>
                                        <h3>&nbsp;</h3>
                                    </th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>
                                        <h3>
                                            <strong>Profit / Loss Before Tax</strong>
                                        </h3>
                                    </th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>Provision For Income Tax</th>
                                    <th>{{number_format($ProvisionForIncomeTax)}}</th>
                                </tr>
                                <tr>
                                    <th id="prof_ater"></th>
                                    <th><h3><strong>Profit / Loss </strong></h3></th>
                                    <th><strong>{{number_format((-$revenue_total-$expense_total)-$ProvisionForIncomeTax)}}</strong></th>

                                </tr>

                                {{--                        <tr>--}}
                                {{--                            <th id="financial_charges"></th>--}}
                                {{--                            <th><strong>FINANCIAL CHARGES</strong></th>--}}
                                {{--                            <th>{{number_format($financial_charges)}}</th>--}}

                                {{--                        </tr>--}}
                                {{--                        <tr>--}}
                                {{--                            <th id="other_income"></th>--}}
                                {{--                            <th><strong>OTHER INCOME</strong></th>--}}
                                {{--                            <th>{{number_format($other_income)}}</th>--}}

                                {{--                        </tr>--}}
                                {{--                        <tr>--}}
                                {{--                            <th id="total_prof"></th>--}}
                                {{--                            <th><h3><strong>PROFIT BEFORE TAXATION</strong></h3></th>--}}
                                {{--                            <th>{{number_format($revenue_total-$expense_total-$financial_charges+$other_income)}}</th>--}}

                                {{--                        </tr>--}}
                                {{--                        <tr>--}}
                                {{--                            <th style="height: 50px;border:none"></th>--}}
                                {{--                            <th style="height: 50px;border:none"></th>--}}
                                {{--                            <th style="height: 50px;border:none"></th>--}}

                                {{--                        </tr>--}}

                                {{--                        <tr>--}}
                                {{--                            <th id="taxation_net"></th>--}}
                                {{--                            <th><strong>TAXATION - NET</strong></th>--}}
                                {{--                            <th>{{number_format($taxation)}}</th>--}}

                                {{--                        </tr>--}}
                                {{--                        <tr>--}}
                                {{--                            <th style="height: 50px;border:none"></th>--}}
                                {{--                            <th style="height: 50px;border:none"></th>--}}
                                {{--                            <th style="height: 50px;border:none"></th>--}}

                                {{--                        </tr>--}}

                                {{--                        <tr>--}}
                                {{--                            <th id="prof_ater"></th>--}}
                                {{--                            <th><h3><strong>PROFIT AFTER TAXATION</strong></h3></th>--}}
                                {{--                            <th>{{number_format(($revenue_total-$expense_total-$financial_charges+$other_income)-$taxation)}}</th>--}}

                                {{--                        </tr>--}}
                                </tbody>
                            </table><br><br>
                        </div>
                    </div>


                </div>

        </div>
    </div>

    @endif
    <script>
        function myFunction() {

            var checkBox = document.getElementById("myCheck");
            if (checkBox.checked == true){
                document.getElementById('date_filter').innerHTML= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">' +
                    '  <label>Month from</label>' +
                    ' <input required name="datefrom" class="form-control" type="month">' +
                    '</div>' +
                    '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">' +
                    '<label>Month To</label>' +
                    '<input required name="dateto" class="form-control" type="month">' +
                    '</div>'
            } else {
                document.getElementById('date_filter').innerHTML= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">' +
                    '  <label>Date from</label>' +
                    ' <input required name="datefrom" class="form-control" type="date">' +
                    '</div>' +
                    '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">' +
                    '<label>Date To</label>' +
                    '<input required name="dateto" class="form-control" type="date">' +
                    '</div>'
            }
        }
    </script>
@endsection








