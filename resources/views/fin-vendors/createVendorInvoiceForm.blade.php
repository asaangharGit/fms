@extends('layouts.master')
@section('page_title', 'Create Vendor Invoice')
@section('content')

    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('fin-vendors.storeVendorInvoice') }}" role="form" enctype="multipart/form-data">
                @csrf

                <div class="box box-info padding-1">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        <button type="button" id="vendorformbtn" class="btn btn-primary btn-xs" onclick="openVendorForm()">Add Vendor</button>
                    </div>

                    <div class="box-body">
                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row" id="vendor_form"></div>
                            <br>
                            <div class="row">

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Vendor</label>
                                    <select required class="form-control" name="vendor_id" id="vendor_id">
                                        <option value="">Select</option>
                                        @foreach($vendors as $row)
                                            <option value="{{$row->id}}">{{$row->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Invoice No.</label>
                                    <input type="text" name="invoice_no" id="invoice_no" value="" required class="form-control" />
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Invoice Date</label>
                                    <input type="date" name="invoice_date" id="invoice_date" value="" required class="form-control" />
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Due Date</label>
                                    <input type="date" name="due_date" id="due_date" value="" required class="form-control" />
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead">
                                        <th style="width: 400px;">Accounts</th>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Tax</th>
{{--                                        <th>Merge tax</th>--}}
                                        <th>Amount</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="appendItems">
                                        @foreach($binding_acc as $key=>$bVal)
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
                                                    @foreach($accounts as $val)
                                                        <option @if($bVal == $val->id) selected @endif value="{{$val->id}}">{{$val->code.' - '.$val->title}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="item_desc[]" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" onkeyup="calculateAmount({{$key}})" id="price_{{$key}}" name="price[]" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" onkeyup="calculateAmount({{$key}})" id="tax_{{$key}}" name="tax[]" value="0" required>
                                            </td>
{{--                                            <td>--}}
{{--                                                <select name="merge[]">--}}
{{--                                                    <option @if($val->merge == 0) selected @endif value="0">No</option>--}}
{{--                                                    <option @if($val->merge == 1) selected @endif value="1">Yes</option>--}}
{{--                                                </select>--}}
{{--                                            </td>--}}
                                            <td>
                                                <input type="number" class="form-control total_rows_invoice" readonly id="total_{{$key}}" name="total[]" required>
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
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 text-right"></div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right">
                                    <label>Total Amount</label>
                                    <input type="number" onkeyup="invoiceCal()" readonly name="invoice_amount" id="invoice_amount" value="" required class="form-control" />
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
    <script>

    </script>

@endsection
