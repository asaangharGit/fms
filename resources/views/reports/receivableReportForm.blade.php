@extends('layouts.master')
@section('page_title', 'Receivable Reports Form')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('reports.receivableReportDetail') }}"  role="form" enctype="multipart/form-data">
                @csrf

                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                                {{ Form::label('Select Customer:') }}
                                <select name="customer_id" id="customer_id" required class="form-control select-search">
                                    <option value="show_all">Show All</option>
                                    @foreach($customers as $row)
                                        <option @if($customer_id == $row->id) selected @endif value="{{$row->id}}">{{$row->account_no}}--{{$row->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <label>Date from</label>
                                <input required name="datefrom" class="form-control <?=($errors->has('datefrom') ? ' is-invalid' : '')?>" type="date" value="{{$datef}}"  max="{{date("Y-m-d")}}">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <label>Date To</label>
                                <input required name="dateto" class="form-control <?=($errors->has('dateto') ? ' is-invalid' : '')?>" type="date" value="{{$datet}}" max="{{date("Y-m-d")}}">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <br>  <br>
                                <label class="radio-inline">
                                    <input type="radio" name="sum_detail" @if($report_type == 'summary')checked @endif value="summary">Summary
                                </label> &nbsp;&nbsp;
                                <label class="radio-inline">
                                    <input type="radio" name="sum_detail"  @if($report_type == 'detail')checked @endif value="detail">Detail
                                </label>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <br>
                                <button type="submit" class="btn btn-primary"><i class="icon-chevron-right"></i>Generate</button>
                            </div>
                        </div>
                    </div>
                    <br>

                </div>
            </form>
            @if($datef != '')

                <div class="table-responsive">
                    <table class="table table-bordered datatable-button-html5-columns">
                        <thead>
                        <tr class="datatable-header center" style="padding: 4px;">
                            <th class="text-center">Sr.#</th>
                            <th>Name</th>
                            <th class="text-right">Total Amount</th>
                            <th class="text-right">Principal</th>
                            <th class="text-right">Profit</th>
                        </tr>
                        </thead>
                            <tbody>
                                @foreach($rec as $key => $val)
                                    <tr>
                                        <td class="text-center">{{$key+1}}</td>
                                        <td>{{$val->name}}</td>
                                        <td class="text-right">{{number_format($val->total_amount)}}</td>
                                        <td class="text-right">{{number_format($val->principal)}}</td>
                                        <td class="text-right">{{number_format($val->profit)}}</td>
                                    </tr>
{{--                                    <tr>--}}
{{--                                        <td colspan="2" class="text-right"><strong>Paid</strong></td>--}}
{{--                                        <td class="text-right">{{number_format($val->paid_total)}}</td>--}}
{{--                                        <td class="text-right">{{number_format($val->paid_principal)}}</td>--}}
{{--                                        <td class="text-right">{{number_format($val->paid_total)}}</td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td colspan="2" class="text-right"><strong>Remaining</strong></td>--}}
{{--                                        <td class="text-right">{{number_format($val->total_amount-$val->paid_total)}}</td>--}}
{{--                                        <td class="text-right">{{number_format($val->principal-$val->paid_principal)}}</td>--}}
{{--                                        <td class="text-right">{{number_format($val->profit-$val->paid_total)}}</td>--}}
{{--                                    </tr>--}}
{{--                                    <tr class="gap_rec">--}}
{{--                                        <td colspan="4"></td>--}}
{{--                                    </tr>--}}
                                @endforeach
                            </tbody>
                    </table>
                </div>
                <br><br>

            @endif
        </div>
    </div>

@endsection
