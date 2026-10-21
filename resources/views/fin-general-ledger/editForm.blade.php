<div class="box box-info padding-1">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <b>{{ App\Http\Controllers\FinGeneralLedgerController::getReference($finGeneralLedger->txn_type,$finGeneralLedger->txn_series,3) }}
                </b>
                <br>  <br>
            </div>
            <div class="col-md-4">
                <label>Reference</label>
                <input type="hidden" name="fin_id" value="{{$finGeneralLedger->id}}">
                <select name="reference" required="required" id="reference" class="form-control">
                    <option value="">Select Payment Type</option>
                    <option @if($finGeneralLedger->txn_type == 1) selected @endif value="1">BPV</option>
                    <option @if($finGeneralLedger->txn_type == 2) selected @endif value="2">BRV</option>
                    <option @if($finGeneralLedger->txn_type == 3) selected @endif value="3">CPV</option>
                    <option @if($finGeneralLedger->txn_type == 4) selected @endif value="4">CRV</option>
                    <option @if($finGeneralLedger->txn_type == 5) selected @endif value="5">JV</option>
                </select>
            </div>


            <div class="col-md-4">
                <label>Vendors</label>
                <select name="vendor_id" id="vendor_id" class="form-control" onchange="getInvoices(this.value)">
                    <option value="">Select</option>
                    @foreach($vendors as $val)
                        <option @if($val->id == $finGeneralLedger->vendor_id) selected @endif value="{{$val->id}}">{{$val->name}}</option>
                    @endforeach

                </select>
            </div>
            <div class="col-md-4">
                <label>Invoices  <span id="vloader"></span></label>
                <select class="form-control" name="invoices_id" id="invoices_id" onchange="getInvoiceAmount(this.value)">
                    <option value="">Select</option>
                     @if($finGeneralLedger->vendor_id != '')
                         @foreach(\App\Models\FinVendorInvoice::where('vendor_id',$finGeneralLedger->vendor_id) as $val)
                            <option value="{{$val->id}}" @if($val->id == $finGeneralLedger->invoices_id) selected @endif>
                                Invoice no => {{$val->invoice_no}} --- Amount => {{$val->total_invoice_amount}}
                            </option>
                         @endforeach
                     @endif
                </select>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-4">
                <label>Bank Chequebook</label>
                <select class="form-control"name="cheque_id" id="cheque_id">
                    <option value="">Select</option>
                    @foreach($banks as $val)
                        <option value="{{$val->id}}">{{$val->banks->bank_name}} - {{$val->chequebook_name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Cheque Number</label>
                <input class="form-control chequenum" type="number" name="chqnum" onchange="chequeNumExit()" value="{{$finGeneralLedger->cheque_number}}" id="chqnum"/>
                <span id="cheq_num" style="color:red;"></span>
            </div>
            <div class="col-md-4">
                <label>Transaction Date</label>
                <input class="form-control" type="date" required="required" name="TxnDate" id="TxnDate" value="{{$finGeneralLedger->txn_date}}" />
            </div>

        </div>

        <hr>
        <div class="row">
            <div class="col-md-2"><h3>Purpose:</h3></div>
            <div class="col-md-10">
                <input class="form-control purpose" required="required" value="{{$finGeneralLedger->details}}" autocomplete="off" name="purpose" id="purpose" placeholder="Purpose" id="purpose" />

            </div>
        </div>
        <div class="row">
            <div class="col-md-2"><h3>Attachments:</h3></div>
            <div class="col-md-10">
                <input type="file" multiple="multiple" id="filesupload" name="filesupload[]" class="form-control alert-primary" />
                <br>
                <?php
                $count = 1;
                $fileList = glob('uploads/'.$finGeneralLedger->id.'/*');
                foreach($fileList as $filename){
                    if(is_file($filename)){
                        echo '<a class="btn btn-xs btn-primary" href="'.url('/').'/'.$filename.'">Attach-'.$count++.'</a>';
                    }
                }
                ?>
            </div>

        </div>

        <hr>

        <div class="row">
            <div class="col-md-10"></div>
            <div class="col-md-1">

            </div>
            <div class="col-md-1">
                <button type="button" class="add-row-edit form-control btn-success"><i class="icon-plus3"></i></button>
            </div>

        </div>
        <hr>
        @php
            $dbRow = $finGeneralLedger->ledgerdetails->count();
        @endphp
        <input type="hidden" id="vmsrows" value="{{$dbRow}}">
        <table class="table table-bordered">

            <tr style="font-size: 18px; font-weight: bold;" class="transaction-heading">
                <td>Sr#</td>
                <td style="width: 30%">Chart of Account</td>
                <td>Detail</td>
                <td>Debit</td>
                <td>Credit</td>
                <td>-</td>
            </tr>
            <tbody id="gl_rows">
            @php

                $counter=0;
                $GrandDebit = 0;
                $GrandCredit = 0;
            @endphp
            @foreach($finGeneralLedger->ledgerdetails as $row)
                @php
                    $counter=$counter+1;

                    $GrandDebit += $row->debit;
                    $GrandCredit += $row->credit;
                @endphp

                <tr class="transaction-data count-vms-rows gl_rows_{{$counter}}" >
                    <td>{{ $counter }}</td>

                    <td>

                        <select style="width: 100%" required name="chartofaccount[]" class="form-control">
                            <option value="0">Select Chart Of Account</option>
                            @foreach($chartOfAccounts as $row2)
                                <option @if($row->coa_id == $row2->id) selected @endif value="{{$row2->id}}">{{$row2->code}} - {{$row2->title}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input required class="form-control inputvalues detail" type="text" autocomplete="off" value="{{$row->detail}}" name="detail[]" id="detail_{{$counter}}" placeholder="Description" id="detail" />

                    </td>
                    <td>
                        <input required onkeyup="debit()" class="form-control inputvalues debit" autocomplete="off" value="{{$row->debit}}" name="debit[]" id="debit_{{$counter}}" placeholder="Debit Amount" />
                    </td>
                    <td>
                        <input required onkeyup="credit()" class="form-control inputvalues credit" autocomplete="off" value="{{$row->credit}}" name="credit[]" id="credit_{{$counter}}" placeholder="Credit Amount" />
                    </td>
                    <td>
                        <button type="button" onclick="removeVmsRow('<?=$counter?>')" class="btn btn-xs btn-danger"><i class="icon-minus3"></i></button>

                    </td>
                </tr>
                <script>
                    $("#chartofaccount_"+{{$counter}}).select2();
                </script>
            @endforeach
            </tbody>

            <tr style="font-size: 18px; font-weight: bold;" class="transaction-heading">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right granddebit1">{{ number_format($GrandDebit,0) }}</td>
                <td class="text-right grandcredit1">{{ number_format($GrandCredit,0) }}</td>
            </tr>

        </table>

    <hr>
    <div class="row">

        <div class="col-md-10 text-right">
            <p id="vms-error" style="color:red;"></p>
        </div>
        <div class="col-md-2 text-right">
            <button type="submit" class="btn btn-success submitbtn">Update</button>
        </div>
    </div>
</div>
</div>
<Script>
    $("#cheque_id").select2();

</Script>