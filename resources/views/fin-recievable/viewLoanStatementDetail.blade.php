@extends('layouts.master')
@section('page_title', 'View Loan Statement Detail')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }

    </style>
    <div class="card">
        <div class="card-body">
           
            <div class="row">
                <div class="col-md-3 label_bg text-center">
                    <h3>CUSTOMER INFO</h3>
                </div>
                <div class="col-md-6"></div>
{{--                <div class="col-md-3 text-center label_bg">--}}
{{--                    <h3>ACCOUNT SUMMARY</h3>--}}
{{--                </div>--}}
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-5">
                    <table class="stat_table2">
                        <tr>
                            <th>Customer Name</th>
                            <td>{{$data->name}}</td>
                        </tr>
                        <tr>
                            <th>Customer no</th>
                            <td>{{$data->account_no}}</td>
                        </tr>
                        <tr>
                            <th>Loan Amount</th>
                            <td> {{number_format($data->total_amount)}}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{\App\Helpers\Qs::dateFormat($request->from_date) .' - '. \App\Helpers\Qs::dateFormat($request->till_date)  }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-5">
{{--                    <table class="stat_table">--}}
{{--                        <tr>--}}
{{--                            <th></th>--}}
{{--                            <th>Opening Balance</th>--}}
{{--                            <th>Closing Balance</th>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>Profit</th>--}}
{{--                            <td>{{number_format($prev->profit-$prev->installments->sum('profit'))}}</td>--}}
{{--                            <td>{{number_format($data->profit-$data->installments->sum('profit'))}}</td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>Principal</th>--}}
{{--                            <td> {{number_format($prev->principal-$prev->installments->sum('principal'))}}</td>--}}
{{--                            <td> {{number_format($data->principal-$data->installments->sum('principal'))}}</td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>Total Balance</th>--}}
{{--                            <td> {{number_format($prev->total_amount-$prev->installments->sum('total_amount')) }}</td>--}}
{{--                            <td>{{number_format($data->total_amount-$data->installments->sum('total_amount')) }}</td>--}}
{{--                        </tr>--}}
{{--                    </table>--}}

                </div>
            </div>
            <br><br>
            <div class="table-responsive">
                <table class="table datatable-button-html5-columns">
                    <thead class="thead">

                   <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Principal</th>
                        <th class="text-center">Profit</th>
                        <th class="text-center">Total</th>

                    </tr>
                   <tr>
                       <th colspan="2"><strong>Opening Balance</strong></th>
                       <th class="text-center"><strong>{{number_format($prev->principal-$prev->installments->sum('principal'))}}</strong></th>
                       <th class="text-center"><strong>{{number_format($prev->profit-$prev->installments->sum('profit'))}}</strong></th>
                       <th class="text-center"><strong>{{number_format($prev->total_amount-$prev->installments->sum('total_amount')) }}</strong></th>
                   </tr>

                    </thead>
                    <tbody>
                    <?php $counter =1;  $total=0;$profit=0;$principal=0; ?>
                    @foreach($data->installments as $val)
                        @php
                            $total+=str_replace(',', '', number_format($val->total_amount));
                            $principal += str_replace(',', '', number_format($val->principal));
                            $profit += str_replace(',', '', number_format($val->profit));
                        @endphp
                        <tr>
                            <td class="text-center">{{$counter++}}</td>
                            <td class="text-center">{{\App\Helpers\Qs::dateFormat($val->recovered_date)}}</td>
                            <td class="text-center">{{number_format($val->principal)}}</td>
                            <td class="text-center">{{number_format($val->profit)}}</td>
                            <td class="text-center">{{number_format($val->total_amount)}}</td>
                        </tr>
                    @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2"><strong>Closing Balance</strong></th>
                            <th class="text-center"><strong>{{number_format($data->principal-$principal)}}</strong></th>
                            <th class="text-center"><strong>{{number_format($data->profit-$profit)}}</strong></th>
                            <th class="text-center"><strong>{{number_format($data->total_amount-$total) }}</strong></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>


@endsection
