@extends('layouts.master')
@section('page_title', 'Vendor Ledger')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('reports.vendorLedgerReportDetail') }}"  role="form" enctype="multipart/form-data">
                @csrf

                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                                {{ Form::label('Select Invoices:') }}
                                <select name="invoice_id" id="invoice_id" required class="form-control select-search">
                                    <option value="">Invoices</option>
                                    @foreach($invoices as $row)
                                        <option @if($invoices_id == $row->id) selected @endif value="{{$row->id}}">{{$row->invoice_no}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Date from</label>
                                <input required name="datefrom" class="form-control <?=($errors->has('datefrom') ? ' is-invalid' : '')?>" type="date" value="{{$datef}}"  max="{{date("Y-m-d")}}">
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Date To</label>
                                <input required name="dateto" class="form-control <?=($errors->has('dateto') ? ' is-invalid' : '')?>" type="date" value="{{$datet}}" max="{{date("Y-m-d")}}">
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

                        <thead style="background-color: #97D35D;">

                        <tr class="datatable-header center" style="padding: 4px;">
                            <th>Sr.#</th>
                            <th>Invoice</th>
                            <th>Vendor</th>
                            <th>Descriptions</th>
                            <th>Invoice Amount</th>
                            <th class="text-right">Paid</th>


                        </tr>

                        </thead>
                            <?php $GrandDebit=0 ?>
                            <?php $GrandCredit=0 ?>
                            <?php $i=0 ?>

                            <tbody>


                            @foreach($ReportData as $Row)
                                @php

                                $invoices = \App\Models\FinVendorInvoice::where('id',$Row->invoices_id);
                                @endphp
                                <tr class="border-blue">
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $invoices->value('invoice_no') }}</td>
                                    <td>{{ \App\Models\FinVendors::where('id',$Row->vendor_id)->value('name') }}</td>
                                    <td> {{ $Row->details }}</td>
                                    <td>{{ number_format($invoices->value('tax_amount') + $invoices->value('invoice_amount')) }}</td>
                                    <td class="text-right">{{ number_format($Row['total_debit']) }}</td>

                                </tr>

                                    <?php $GrandDebit+=$Row['total_debit'] ?>

                            @endforeach

                            </tbody>

                        <tr class="border-blue" style="border-color: #97D35D;border-width: 2px 0px 2px 0px;border-style: double;">
                            <td colspan="4" class="text-right"><b>Total</b></td>
                            <td class="text-right"><b>{{ number_format($GrandDebit) }}</b></td>

                        </tr>
                    </table>
                </div>
                <br><br>

            @endif
        </div>
    </div>

@endsection
