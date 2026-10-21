@extends('layouts.master')
@section('page_title', 'Create Vendor Jv')
@section('content')

    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('fin-general-ledgers.store') }}" id="myform" role="form" enctype="multipart/form-data">
                @csrf
                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Reference</label>
                                <select name="reference" required="required" id="reference" class="form-control">

{{--                                    <option value="1">BPV</option>--}}
{{--                                    <option value="2">BRV</option>--}}
                                    <option selected value="3">CPV</option>
{{--                                    <option value="4">CRV</option>--}}
{{--                                    <option value="5">JV</option>--}}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Transaction Date</label>
                                <input class="form-control" type="date" required="required" name="TxnDate" id="TxnDate" value="{{$TxnDate}}" />
                            </div>
                            <div class="col-md-3 chequenum">
                                <label>Cheque Number</label>
                                <input class="form-control chequenum" type="number" name="chqnum" id="chqnum" value="{{$chqnum}}" />
                            </div>
                            <div class="col-md-3">
                                <label>Vendors</label>
                                <select class="form-control" name="vendor_id" id="vendor_id">
                                    <option value="">Select</option>
                                    @foreach($vendors as $val)
                                        <option value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="hidden" id="vmsrows" value="0">

                        <hr>
                        <div class="row">
                            <div class="col-md-2"><h3>Purpose:</h3></div>
                            <div class="col-md-10">
                                <input class="form-control purpose" required="required" autocomplete="off" name="purpose" id="purpose" placeholder="Purpose" id="purpose" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2"><h3>Attachments:</h3></div>
                            <div class="col-md-10">
                                <input type="file" multiple="multiple" id="filesupload" required="" name="filesupload[]" class="form-control alert-primary" />
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-10"></div>
                            <div class="col-md-1">

                            </div>
                            <div class="col-md-1">
                                <button onclick="test()" type="button" class="add-row form-control btn-success"><i class="icon-plus3"></i></button>
                            </div>
                        </div>
                        <hr>
                        <div class="row" style="font-size: 18px; font-weight: bold;">
                            <div class="col-md-1">S#</div>
                            <div class="col-md-4">Account</div>
                            <div class="col-md-3">Description</div>
                            <div class="col-md-2">Debit</div>
                            <div class="col-md-2">Credit</div>

                        </div>
                        <div id="gl_rows">
                            <div class="row count-vms-rows gl_rows_1">
                                <div class="col-md-1">
                                    1
                                </div>
                                <div class="col-md-3">
                                    <select required name="chartofaccount[]" id="chartofaccount_1" class="form-control">
                                        <option value="0">Select Chart Of Account</option>
                                        @foreach($chartOfAccounts as $row)
                                            <option value="{{$row->id}}">{{$row->code}} - {{$row->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input required class="form-control inputvalues detail" type="text" autocomplete="off" name="detail[]" id="detail_1" placeholder="Description" id="detail" />
                                </div>
                                <div class="col-md-2">
                                    <input required onkeyup="debit()" class="form-control inputvalues debit" autocomplete="off" value="0" name="debit[]" id="debit_1" placeholder="Debit Amount" />
                                </div>
                                <div class="col-md-2">
                                    <input required onkeyup="credit()" class="form-control inputvalues credit" autocomplete="off" value="0" name="credit[]" id="credit_1" placeholder="Credit Amount" />
                                </div>
                                <div class="col-md-1">
                                    -
                                </div>
                            </div>

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
