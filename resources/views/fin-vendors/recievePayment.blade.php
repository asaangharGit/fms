@extends('layouts.master')
@section('page_title', 'Receive Payment Against Invoice')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Payment Against Invoice</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('fin-vendors.storeRecievedPayment') }}" id="myform" role="form" enctype="multipart/form-data">
                @csrf
                <div class="box box-info padding-1 iamount">
                    <input type="hidden" id="invoice_paid_amount" value="">
                    <input type="hidden" id="invoice_date" value="">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Invoice no</label>
                                <input readonly class="form-control" value="{{$invoice->invoice_no}}">
                                <input type="hidden" name="invoice_id" class="form-control" value="{{$invoice->id}}">
                            </div>
                            <div class="col-md-3">
                                <label> Date :</label>
                                <input readonly class="form-control" value="{{\App\Helpers\qs::dateFormat($invoice->invoice_date)}}">
                            </div>
                            <div class="col-md-3">
                                <label> Amount</label>
                                <input readonly class="form-control" value="{{number_format($invoice->invoice_amount)}}">
                            </div>
                            <div class="col-md-3">
                                <label>Vendor</label>
                                <input readonly class="form-control" value="{{$invoice->vendors->name}}">
                                <input type="hidden" name="vendor_id" class="form-control" value="{{$invoice->vendors->id}}">
                            </div>

                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Bank Chequebook</label>
                                <select class="form-control select2" name="cheque_id" id="cheque_id">
                                    <option value="">Select</option>
                                    @foreach($banks as $val)
                                        <option value="{{$val->id}}">{{$val->banks->bank_name}} - {{$val->chequebook_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Cheque Number</label>
                                <input class="form-control chequenum" type="number" name="chqnum" id="chqnum" readonly/>
                                <span id="cheq_num" style="color:red;"></span>
                            </div>
                            <div class="col-md-4">
                                <label>Transaction Date</label>
                                <input class="form-control" type="date" required="required" name="TxnDate" id="TxnDate" value="<?=date('Y-m-d')?>" />
                            </div>


                        </div>
                        <input type="hidden" id="vmsrows" value="0">

                        <br>
                        <div class="row">
                            <div class="col-md-2"><h3>Purpose:</h3></div>
                            <div class="col-md-10">
                                <input class="form-control purpose" value="Recieving amount against invoice {{$invoice->invoice_no}}" required="required" autocomplete="off" name="purpose" id="purpose" placeholder="Purpose" id="purpose" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-10"></div>
                            <div class="col-md-1">

                            </div>

                        </div>
                   
                        <div class="row" style="font-size: 18px; font-weight: bold;">
                            <div class="col-md-1">S#</div>
                            <div class="col-md-4">Account</div>
                            <div class="col-md-3">Description</div>
                            <div class="col-md-2">Debit</div>
                            <div class="col-md-2">Credit</div>

                        </div>
                        <div id="gl_rows">
                            @foreach($accrows as $key=>$rVal)
                            <div class="row count-vms-rows gl_rows_{{$key}}">
                                <div class="col-md-1">{{$key+1}}</div>
                                <div class="col-md-3">
                                    <select required name="chartofaccount[]" id="chartofaccount_{{$key}}" class="form-control">
                                        @foreach($chartOfAccounts as $row)
                                            @if($row->id == $rVal)
                                                 <option value="{{$row->id}}">{{$row->code}} - {{$row->title}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input required class="form-control inputvalues detail" type="text" value="-" autocomplete="off" name="detail[]" id="detail_{{$key}}" placeholder="Description" id="detail" />
                                </div>
                                <div class="col-md-2">
                                    <input required onkeyup="debit()" class="form-control inputvalues debit" autocomplete="off" value="0" name="debit[]" id="debit_{{$key}}" placeholder="Debit Amount" />
                                </div>
                                <div class="col-md-2">
                                    <input required onkeyup="credit()" class="form-control inputvalues credit" autocomplete="off" value="0" name="credit[]" id="credit_{{$key}}" placeholder="Credit Amount" />
                                </div>
                                <div class="col-md-1">
                                    -
                                </div>
                            </div>
                           @endforeach
                        </div>
                        <hr>
                        <div class="row" style="font-size: 18px; font-weight: bold;">
                            <div class="col-md-8" align="right">Total</div>
                            <div class="col-md-2 granddebit1 form-control"></div>
                            <div class="col-md-2 grandcredit1 form-control"></div>

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

            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
        $("#cheque_id").select2()


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
