@extends('layouts.master')
@section('page_title', 'Create Jv for Receivables')
@section('content')
    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Jv for Receivables</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('fin-recievable.storeRecievableJv') }}" id="myform" role="form" enctype="multipart/form-data">
                @csrf
                    <div class="box box-info padding-1 iamount">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Customer :</h3>
                                <h3> {{$installment->loan->name }}</h3>
                            </div>
                            <div class="col-md-6">
                                <label>Transaction Date</label>
                                <input class="form-control" type="date"  name="TxnDate" id="TxnDate" value="2024-{{date('m-d',strtotime($installment->recovered_date))}}" />
                            </div>
                          </div>
                        <input type="hidden" id="vmsrows" value="0">
                        <input type="hidden" name="loan_id" value="{{$installment->loan_id}}">
                        <input type="hidden" name="installment_id" value="{{$installment->id}}">

                        <hr>
                        <div class="row">
                            <div class="col-md-2"><h3>Purpose:</h3></div>
                            <div class="col-md-10">
                                <input class="form-control purpose" value="Installment of {{$installment->loan->name}} for the month of {{date('M-y',strtotime($installment->recovered_date))}}" autocomplete="off" name="purpose" id="purpose" placeholder="Purpose" id="purpose" />
                            </div>
                        </div>
                        <hr>
                        <div class="row" style="font-size: 18px; font-weight: bold;">
                            <div class="col-md-1">S#</div>
                            <div class="col-md-3">Account</div>
                            <div class="col-md-3">Description</div>
                            <div class="col-md-2">Debit</div>
                            <div class="col-md-2">Credit</div>
                            <div class="col-md-1"></div>
                        </div>
                        <div id="gl_rows">
                            @php $counter = 1; $debit=0; $credit=0; @endphp
                            @foreach($jv as $key=>$val)
                                @php
                                    $debit += $val['debit'];
                                    $credit += $val['credit'];
                                @endphp
                             <div class="row count-vms-rows gl_rows_{{$counter}}">
                                <div class="col-md-1">{{$counter++}}</div>
                                <div class="col-md-3">
                                    <select name="chartofaccount[]" class="form-control">
                                            <option value="{{$val['account']->id}}">{{$val['account']->code.' - '.$val['account']->title}}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control inputvalues detail" type="text" name="detail[]" id="detail_1" value="{{$val['desc']}}" placeholder="Description" id="detail" />
                                </div>
                                <div class="col-md-2">
                                    <input readonly class="form-control inputvalues debit" value="{{(($val['debit']))}}" name="debit[]" id="debit_1" placeholder="Debit Amount" />
                                </div>
                                <div class="col-md-2">
                                    <input readonly class="form-control inputvalues credit" value="{{($val['credit'])}}" name="credit[]" id="credit_1" placeholder="Credit Amount" />
                                </div>
                                <div class="col-md-1">

                                </div>
                            </div>
                            @endforeach
                        </div>
                        <hr>
                        <div class="row" style="font-size: 18px; font-weight: bold;">
                            <div class="col-md-8" align="right">Total</div>
                            <div class="col-md-2 granddebit1 form-control">{{number_format($debit)}}</div>
                            <div class="col-md-2 grandcredit1 form-control">{{number_format($credit)}}</div>

                        </div>

                    </div>
                    <hr>
                    <div class="row">

                        <div class="col-md-10 text-right">
                            <p id="vms-error" style="color:red;"></p>
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="submit" class="btn btn-success submitbtn">Submit</button>
                        </div>
                    </div>
                </div>

                <Script>
                    $("#cheque_id").select2();

                </Script>

            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {


            $("select").select2();
            $("#reference").change(function () {
                console.log(parseInt($("#reference").val()));
                if (parseInt($("#reference").val()) == 5) {
                    //$("#reference").val();
                    console.log("thats it: " + parseInt($("#reference").val()));
                    //$("#chqnum").attr("disabled", "disabled");
                    $(".chequenum").hide();
                } else {
                    $(".chequenum").show();
                }
            });


        });
    </script>
@endsection
