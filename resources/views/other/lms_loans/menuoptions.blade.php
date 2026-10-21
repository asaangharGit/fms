@extends('layouts.master')
@section('page_title', 'Manage Financing')
@section('content')
    <style>
        .menuopt a {
            color: #FFFFFF !important;
            font-weight: bolder  !important;


        }
        .menuopt .dropdown-item {
            color: #000000 !important;
            font-weight: bolder  !important;

        }
        .labelinner {
            position: relative;
            /* margin-top: -10px; */
            /* margin-left: -15px; */
            /* margin-bottom: 30px; */
            /* background: darkorange; */
            color: lightcoral;
            /* padding: 2px 10px 2px 10px; */
            font-weight: 600;
            /*border-radius: 3px;*/
            text-decoration: underline;
        }
        .myTable td {
            width:20%;
        }
        #dropdownMenuButton {
            font-weight: bolder !important;
        }

        .rightoptions {
            align-items: flex-start;
            justify-content: left;
            display: flex;
            width: 190px;    }
        .rightoptions i {
            float: right;
            vertical-align: middle;
            position: absolute;
            right: 0px;
            margin: 10px;
        }

    </style>
    <div class="card">

        <!-- Kibor Tenure Modal -->
        <div class="modal fade" id="kiborTenureModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Kibor Tenure</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <select id="kibor_rev" class="col-md-12">
                                <option value="1">01 Year</option>
                                <option value="3">03 Year</option>
                                <option value="5">05 Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveKibor('kiborTenure')">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Processing Modal -->
        <div class="modal fade" id="processingfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Processing Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input class="form-control" type="number" placeholder="Enter Fees" id="processingfees" />
                        </div>
                        <div class="row form-check">
                            <input class="form-check-input" type="checkbox" value="" id="reverse">
                            <label class="form-check-label" for="reverse">
                                Reverse Entry
                            </label>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveFees('processingfees')">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FED Modal -->
        <div class="modal fade" id="fedfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">FED Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="number" placeholder="Enter Fees" id="fedfees" />
                        </div>

                        <div class="row form-check">
                            <input class="form-check-input" type="checkbox" value="" id="reverse">
                            <label class="form-check-label" for="reverse">
                                Reverse Entry
                            </label>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveFees('fedfees')">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Legal Modal -->
        <div class="modal fade" id="legalfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Legal Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <select class="select form-control" id="legalCompanyId">
                                <option value="">Select Legal Company</option>
                                @foreach($legal as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <input class="form-control" type="number" placeholder="Enter Fees" id="legalfees" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveFees('legalfees')">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Valuation Modal -->
        <div class="modal fade" id="valuationfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Valuation Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <select class="select form-control" id="valuationCompanyId">
                                <option value="">Select Legal Company</option>
                                @foreach($valuation as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <input class="form-control" type="number" placeholder="Enter Fees" id="valuationfees" />
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveFees('valuationfees')">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Income Modal -->
        <div class="modal fade" id="incomefeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Income Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <label>Income Estimation Company:</label>
                            <select class="select form-control" id="incomeEstCompanyId">
                                <option value="">Select Legal Company</option>
                                @foreach($income as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <label>Income Estimation Amount:</label>
                            <input class="form-control" type="number" placeholder="Enter Fees" id="incomefees" />
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveFees('incomefees')">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stamp Modal -->
        <div class="modal fade" id="stampfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Stamp Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="number" placeholder="Enter Fees" id="stampfees" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveFees('stampfees')">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Lien Modal -->
        <div class="modal fade" id="lienfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Lien Amount</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="number" placeholder="Enter Amount" id="lienfees" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveFees('lienfees')">Save changes</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="card-header header-elements-inline">

            <h6 class="card-title">Generate Financing Schedule</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                @if(Qs::userIsTeamSA())
                    <li class="nav-item"><a href="#add-tt" class="nav-link active" data-toggle="tab">Create Schedule</a></li>
                @endif
            </ul>


            <div class="menuopt tab-content">
                <div class="row col-md-12">
                    <div class="col-md-2">

                        <div class="dropdown">
                            <button class="btn btn-success col-md-12 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Legal Documents
                            </button>
                            <div class="dropdown-menu success" aria-labelledby="dropdownMenuButton">
                                @foreach($files as $key=>$file)
                                    <a class="dropdown-item" href="{{ route('ttr.legal_docs', ['loanId'=>$loanId,'doc_id'=>$key, 'loantypeid'=>$data->loan_type_id])}}">{{ ++$key }}: {{ $file }}.docx</a>
                                @endforeach
                            </div>
                        </div>


                        <!--<a href="{{ url('/items/legalDocuments.zip')}}" class="btn btn-success col-md-12">Legal Documents</a>-->
                        <!--                    <a href="{{ route('ttr.legal_docs', $loanId)}}" class="btn btn-success col-md-12">Legal Documents</a>-->
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('ttr.show_schedule', $loanId) }}" class="btn btn-success col-md-12">Repayment Schedule</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('loans.takaful', $loanId) }}" class="btn btn-success col-md-12">Takaful Policy</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('loans.pay', $loanId) }}" class="btn btn-success col-md-12">Pay Installment</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('loans.partial', $loanId) }}" class="btn btn-success col-md-12">Partial Payment</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('loans.early', $loanId) }}" class="btn btn-success col-md-12">Early Settlement</a>
                    </div>
                </div>
                <br>
                <br>
                <div class="row col-md-12">
                    <table class="myTable table col-md-12" border="1" cellpadding="5" cellspacing="0">
                        <tr>
                            <td><span class="labelinner">Product Name:</span> <br><strong>{{ $data->loantype->name }}</strong></td>
                            <td><span class="labelinner">LOS Customer ID:</span><br><strong>AGFL000{{ $loanId }}</strong>
                                <input type="hidden" value="{{ $loanId }}" id="loan_id" />
                                <input type="hidden" value="{{ csrf_token() }}" id="_token" />
                            </td>
                            <td><span class="labelinner">Customer Name:</span><br><strong>{{ $data->loan_borrower->fname }}</strong></td>
                            <td><span class="labelinner">CNIC:</span><br><strong>{{ $data->loan_borrower->cnic }}</strong></td>
                            <td><span class="labelinner">Property Purchase Amount:</span><br><strong>{{ number_format($data->property_amount,0) }}</strong></td>
                            <td width="20%" rowspan="7">
                                <div></div>
                                <a class="rightoptions btn btn-primary col-md-12 mb-1" data-toggle="modal" data-target="#kiborTenureModal">Kibor Tenure {!! (isset($data->kibor_revision_cycle) && $data->kibor_revision_cycle) ? "&nbsp;<i class='icon-checkmark' ></i>" : "" !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal" data-target="#processingfeesModal">Processing Fees {!! (isset($loanfees->processingFeesStatus) && $loanfees->processingFeesStatus) ? "&nbsp;<i class='icon-checkmark' ></i>" : "" !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal" data-target="#fedfeesModal">FED Amount{!! (isset($loanfees->fedFeesStatus) && $loanfees->fedFeesStatus) ? "&nbsp;<i class='icon-checkmark' ></i>" : "" !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal" data-target="#legalfeesModal">Legal Company{!! (isset($loanfees->legalFeesStatus) && $loanfees->legalFeesStatus) ? "&nbsp;<i class='icon-checkmark' ></i>" : "" !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal" data-target="#valuationfeesModal">Valuation Company{!! (isset($loanfees->valuationFeesStatus) && $loanfees->valuationFeesStatus) ? "&nbsp;<i class='icon-checkmark' ></i>" : "" !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal" data-target="#incomefeesModal">Income Est. Company{!! (isset($loanfees->incomeEstFeesStatus) && $loanfees->incomeEstFeesStatus) ? "&nbsp;<i class='icon-checkmark' ></i>" : "" !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal" data-target="#stampfeesModal">Stamp Paper Charges{!! (isset($loanfees->stampPaperFeesStatus) && $loanfees->stampPaperFeesStatus) ? "&nbsp;<i class='icon-checkmark' ></i>" : "" !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal" data-target="#lienfeesModal">Lien Amount{!! (isset($loanfees->lienFeesStatus) && $loanfees->lienFeesStatus) ? "&nbsp;<i class='icon-checkmark' ></i>" : "" !!}</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="labelinner">Finance Amount:</span><br><strong>{{ number_format($data->total_amount_pr,0) }}</strong></td>
                            <td><span class="labelinner">Loan Account Number:</span><br><strong>{{ $data->account_no }}</strong></td>
                            <td><span class="labelinner">Tenure in Years:</span><br><strong>{{ $data->loan_period/12 }}</strong></td>
                            <td><span class="labelinner">Tenure in Months:</span><br><strong>{{ $data->loan_period }}</strong></td>
                            <td><span class="labelinner">Customer Equity:</span><br><strong>{{ number_format(($data->property_amount-$data->total_amount_pr),0) }}</strong></td>

                        </tr>
                        <tr>
                            <td><span class="labelinner">Musharakah Date:</span><br><strong>{{ $data->musharakah_date ? date("j M, Y", strtotime($data->musharakah_date)) : "" }}</strong></td>
                            <td><span class="labelinner">Disbursement Date:</span><br> <strong>{{ date("j M, Y", strtotime($data->disb_date)) }}</strong></td>
                            <td><span class="labelinner">1st Installment Date:</span><br><strong>{{ date("j M, Y", strtotime($data->due->ins_date)) }}</strong></td>
                            <td><span class="labelinner">Maturity Date:</span><br><strong>{{ date("j M, Y", strtotime($data->due->mat_date)) }}</strong></td>
                            <td><span class="labelinner">AGFL Finance:</span><br><strong>{{ number_format($data->total_amount_pr,0) }}</strong></td>

                        </tr>
                        <tr>
                            <td><span class="labelinner">Kibor:</span><br><strong>{{ number_format($data->kibor_rate,2) }}%</strong></td>
                            <td><span class="labelinner">Spread:</span><br><strong>{{ number_format($data->spread_rate,2) }}%</strong></td>
                            <td><span class="labelinner">Total Rate:</span><br><strong>{{ number_format(($data->spread_rate+$data->kibor_rate),2) }}%</strong></td>

                            <td><span class="labelinner">Renewal of Kibor:</span><br>
                                <strong>
                                    {{ $data->kibor_revision_cycle ? ($data->kibor_revision_cycle==1 ? $data->kibor_revision_cycle." Year" : $data->kibor_revision_cycle." Years") : "Not Set" }}
                                </strong>
                                <input type="hidden" id="loan_id" value="{{ $loanId }}" />
                            </td>
                            <td><span class="labelinner">Reschedule Kibor Date:</span><br><strong>{{ date("j M, Y", strtotime($data->kibor_revision_date)) }}</strong></td>
                        </tr>
                    </table>
                </div>
                <br><br>
                <div class="row col-md-12">
                    <div class="col-md-2">
                        <button class="btn btn-success dropdown-toggle col-md-12" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <strong id="acknow_letter"> Acknowledgement</strong>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li class="dropdown-item">
                                <a style="color:black !important;" onclick="generateAcknowledgementLetter('{{ route('loans.generate_acknowledgement_letter', $loanId)  }}')">Generate Letter</a>
                            </li>
                            <li class="dropdown-item">
                                <a style="color:black !important;" href="{{ route('loans.upload_acknowledgement_form', $loanId)  }}">Uploaded Docs List</a>
                            </li>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <a id="welcome_letter" onclick="welcome_letter('{{ route('loans.generate_welcome_letter', $loanId)  }}')" class=".dropdown-item btn btn-success col-md-12">Welcome Letter</a>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-success col-md-12" href="{{ route('runkibor', $loanId) }}">Renewal Kibor Schedule</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('loans.taxcertificate', $loanId) }}" class="btn btn-success col-md-12">Tax Certificate</a>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-success col-md-12">Tenure Rescheduling</a>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-success col-md-12">Enhancement</a>
                    </div>
                </div>
                <br>
                <div class="row col-md-12">
                    <div class="col-md-12 mb-2">
                        <a id="storestep" href="{{ route('storestep', $loanId) }}" class="btn btn-primary col-md-12  mb-2" >Disburse the borrower</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function disburse(){
            $(".ui-pnotify").children(".ui-pnotify-text").html("Hello Teerath");
            $(".ui-pnotify").show();
            //href="{{ route('storestep', $loanId) }}"

            //ui-pnotify-text
            //ui-pnotify
            //onclick="disburse()"
//        var kibor_rev_tenure = $("#kibor_rev").val();
//        var loanId = $("#loan_id").val();
//        if(kibor_rev_tenure!=0){
//            var payload = {"_token": $("#_token").val(), loanId: $("#loan_id").val(), kibor_rev: kibor_rev_tenure};
//            $.post("/storestep", payload, function (data) {
//    //            alert(data);
//                if (data.success == true) {
//                    alert("Successfully Saved");
//                    $("#" + id + "Modal").modal('toggle');
//                    location.reload();
//                }
//            });
//
//        } else {
//            alert("Please select kibor revision tenure");
//        }
        }


        function saveFees(id) {
            var valueof = $("#" + id).val();
            if(valueof==""){
                alert("Fees must not be blank");
                return;
            }
//        alert(id);
            if (id == "incomefees")
                var payload = {"_token": $("#_token").val(), loan_id: $("#loan_id").val(), company_id: $("#incomeEstCompanyId").val(), fieldkey: id, fieldvalue: $("#" + id).val()};
            else if (id == "valuationfees")
                var payload = {"_token": $("#_token").val(), loan_id: $("#loan_id").val(), company_id: $("#valuationCompanyId").val(), fieldkey: id, fieldvalue: $("#" + id).val()};
            else if (id == "legalfees")
                var payload = {"_token": $("#_token").val(), loan_id: $("#loan_id").val(), company_id: $("#legalCompanyId").val(), fieldkey: id, fieldvalue: $("#" + id).val()};
            else if (id == "fedfees") {
                var reverse = $("#reverse").is(':checked');
                if (reverse == true) {
                    $("#" + id).val(0);
                }
//            alert("Checked: " + reverse);
                var payload = {"_token": $("#_token").val(), loan_id: $("#loan_id").val(), fieldkey: id, fieldvalue: $("#" + id).val(), rev: reverse};
            } else if (id == "processingfees") {
                var reverse = $("#reverse").is(':checked');
                if (reverse == true) {
                    $("#" + id).val(0);
                }
//            alert("Checked: " + reverse);
                var payload = {"_token": $("#_token").val(), loan_id: $("#loan_id").val(), fieldkey: id, fieldvalue: $("#" + id).val(), rev: reverse};
            } else
                var payload = {"_token": $("#_token").val(), loan_id: $("#loan_id").val(), fieldkey: id, fieldvalue: $("#" + id).val()};


            $.post("/savefeesdata", payload, function (data) {
//            alert(data);
                if (data.success == true) {
                    alert("Successfully Saved");
                    $("#" + id + "Modal").modal('toggle');
                    location.reload();
                }
            });

        }
        function saveKibor(id){
            var payload = {"_token": $("#_token").val(), loanId: $("#loan_id").val(), kibor_rev: $("#kibor_rev").val()};
            $.post("/loans/storestepkibor", payload, function (data) {
                if (data.success == true) {
                    alert("Successfully Saved");
                    $("#" + id + "Modal").modal('toggle');
                    location.reload();
                }
            });

        }
    </script>
    {{--TimeTable Ends--}}

@endsection
