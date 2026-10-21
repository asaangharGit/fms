@extends('layouts.master')
@section('page_title', 'Edit Vendor Invoice')
@section('content')

    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('fin-vendors.VendorInvoiceUpdate',$data_inv->id) }}" role="form" enctype="multipart/form-data">
                @csrf
                {{ method_field('PATCH') }}
                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="card-body">

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Vendor</label>
                                    <select required class="form-control" name="vendor_id" id="vendor_id">
                                        <option value="">Select</option>
                                        @foreach($vendors as $row)
                                            <option @if($data_inv->vendor_id == $row->id) selected @endif value="{{$row->id}}">{{$row->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Invoice No.</label>
                                    <input type="text" name="invoice_no" id="invoice_no" value="{{$data_inv->invoice_no}}" required class="form-control" />
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Invoice Date</label>
                                    <input type="date" name="invoice_date" id="invoice_date" value="{{$data_inv->invoice_date}}" required class="form-control" />
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Due Date</label>
                                    <input type="date" name="due_date" id="due_date" value="{{$data_inv->due_date}}" required class="form-control" />
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
                                            <th></th>
                                            </thead>
                                            <tbody id="appendItems">
                                            @foreach($data_inv->invoice_detail as $key=>$val)
                                                @php
                                                    if ($key < 2) {
                                                              $type = "debit";
                                                        }else{
                                                              $type = 'credit';
                                                        }
                                                @endphp
                                                <tr class="rows_invoice" id="rows_invoice_{{++$key}}">
                                                    <td>
                                                        <select id="chartofaccount_1" name="chartofaccount[]" class="form-control select2" required>
                                                            <option>Select</option>
                                                            @foreach($accounts as $val2)
                                                                <option @if($val->coa_id == $val2->id) selected @endif value="{{$val2->id}}">{{$val2->code.' - '.$val2->title}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="item_desc[]" required value="{{$val->item_desc}}">
                                                    </td>

                                                    <td>
                                                        <input type="number" class="form-control" onkeyup="calculateAmount({{$key}})" id="tax_{{$key}}"  value="{{$val->tax}}" name="tax[]" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" onkeyup="calculateAmount({{$key}})" id="price_{{$key}}" value="{{$val->price}}" name="price[]" required>
                                                    </td>
                                                    <td>
                                                        <select name="merge[]">
                                                            <option @if($val->merge == 0) selected @endif value="0">No</option>
                                                            <option @if($val->merge == 1) selected @endif value="1">Yes</option>
                                                        </select>

                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control total_rows_invoice" readonly id="total_{{$key}}" value="{{$val->total}}" name="total[]" required>
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
                                    <input type="text" readonly onkeyup="invoiceCal()" name="invoice_amount" id="invoice_amount" value="{{$data_inv->invoice_amount}}" required class="form-control" />
                                </div>

                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                    {{ Form::submit('Submit', ['class' => 'btn btn-sm btn-success']) }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </form>
        </div>
    </div>


@endsection
