

<div class="box box-info padding-1 iamount">
<input type="hidden" id="invoice_paid_amount" value="">
    <input type="hidden" id="invoice_date" value="">
    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                <label>Reference</label>
                <select name="reference" required="required" id="reference" class="form-control">
                    <option value="">Select Payment Type</option>
                    <option value="1">BPV</option>
                    <option value="2">BRV</option>
                    <option value="3">CPV</option>
                    <option value="4">CRV</option>
                    <option value="5">JV</option>
                </select>
            </div>

            <div class="col-md-4">
                <label>Vendors</label>
                <select class="form-control" name="vendor_id" id="vendor_id" onchange="getInvoices(this.value)">
                    <option value="">Select</option>
                    @foreach($vendors as $val)
                        <option value="{{$val->id}}">{{$val->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Invoices  <span id="vloader"></span></label>
                <select class="form-control" name="invoices_id" id="invoices_id" onchange="getInvoiceAmount(this.value)">
                    <option value="">Select</option>

                </select>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-4">
                <label>Bank Chequebook</label>
                <select class="form-control" name="cheque_id" id="cheque_id">

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
                <input class="form-control" type="date" required="required" name="TxnDate" id="TxnDate" value="{{$TxnDate}}" />
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
                <button type="button" class="add-row form-control btn-success"><i class="icon-plus3"></i></button>
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

<Script>

</Script>