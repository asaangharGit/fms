@extends('layouts.master')
@section('page_title', 'View Vendor Invoice')
@section('content')

    <div class="card card-default">
        <div class="card-body">
               <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead">
                                                <th>Vendor</th>
                                                <td>{{$data_inv->vendors->name}}</td>
                                                <th>Invoice No#</th>
                                                <td>{{$data_inv->invoice_no}}</td>
                                                <th>Invoice date</th>
                                                <td>{{$data_inv->invoice_date}}</td>
                                                <th>Due Date</th>
                                                <td>{{$data_inv->due_date}}</td>
                                            </thead>
                                            <tbody>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <br>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead">
                                            <th style="width: 300px;">Accounts</th>
                                            <th>Item</th>
                                            <th>Tax</th>
                                            <th>Price</th>
                                            <th>Merge tax</th>
                                            <th>Amount</th>
                                            <th>Transaction</th>
                                            </thead>
                                            <tbody id="appendItems">
                                            @foreach($data_inv->invoice_detail as $key=>$val)
                                                @php
                                                    if ($key < 2 ) {
                                                              $type = "debit";
                                                        }else{
                                                              $type = 'credit';
                                                        }
                                                @endphp
                                                <tr>
                                                    <td>{{$val->account_name->code.' - '.$val->account_name->title}}</td>
                                                    <td>
                                                        {{$val->item_desc}}
                                                    </td>

                                                    <td>
                                                        {{$val->tax}}
                                                    </td>
                                                    <td>
                                                        {{$val->price}}
                                                    </td>
                                                    <td>
                                                        @if($val->merge == 0) No @endif
                                                        @if($val->merge == 1) Yes @endif

                                                    </td>
                                                    <td>
                                                        {{$val->total}}
                                                    </td>
                                                    <td>{{$type}}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"></div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"></div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Invoice Amount</label>
                                    <input type="text" readonly  name="invoice_amount" id="invoice_amount" value="{{$data_inv->invoice_amount}}" required class="form-control" />
                                </div>

                            </div>
                            <br>
                        </div>
                    </div>

                </div>

        </div>
    </div>


@endsection
