@extends('layouts.master')
@section('page_title', 'General Ledger')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
<div class="card card-default">
    <div class="card-body">
        <form method="POST" action="{{ route('reports.generalLegderReportDetail') }}"  role="form" enctype="multipart/form-data">
            @csrf

            <div class="box box-info padding-1">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            {{ Form::label('Select Chart of Account:') }}
                            <select name="chartofaccount" id="chartofaccount" required class="form-control select-search">
                                <option value=""> Chart Of Account 1</option>
                                @foreach($chartOfAccounts as $row)
                                    <option @if($chartofaccount_id == $row->code) selected @endif value="{{$row->code}}">{{$row->code}} - {{$row->title}}</option>
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

                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <br>
                            {{ Form::label('Select Chart of Account:') }}
                            <select name="chartofaccount2" id="chartofaccount" required class="form-control select-search">
                                <option value=""> Chart Of Account 2</option>
                                @foreach($chartOfAccounts as $row)
                                    <option @if($chartofaccount_id2 == $row->code) selected @endif value="{{$row->code}}">{{$row->code}} - {{$row->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <br>
                <div class="box-footer mt20 text-right">
                    <button type="submit" class="btn btn-primary"><i class="icon-chevron-right"></i>Generate</button>
                </div>
            </div>
        </form>
        @if($chartofaccount_id != '')

        <fieldset>
            <h3 class="card-title">Generated from {{ $datef }} to {{ $datet }}</h3>

         </fieldset>


            <div class="table-responsive">

                <div class="text-right">
                    <input class="btn btn-xs btn-primary" type="button" onclick="tableToExcel('finreport', 'W3C Example Table')" value="Export to Excel">
                </div>

                <br>
                <table id="finreport" class="table table-bordered">

                    <thead style="background-color: #97D35D;">

                    <tr class="datatable-header center" style="padding: 4px;">
                        <th width="5%">Sr.#</th>
                        <th id="sortt" width="20%">Code</th>
                        <th width="20%">Title</th>
                        <th width="30%">Descriptions</th>
                        <th width="15%">Transaction</th>
                        <th  style="text-align:right" width="10%">Debit</th>
                        <th style="text-align:right" width="10%">Credit</th>
                        <th style="text-align:right" width="12%">Balance</th>
                        <th>-</th>

                    </tr>

                    </thead>
                    <tbody>
                    <?php $i=0;
                        $credit_final = 0;
                        $debit_final = 0;
                        $opening_debit=0;
                        $opening_credit = 0;
                        $credit_cal = 0;
                        $debit_cal = 0;
                        $final_cal = 0;
                        // Running balance shown like the Closing Balance amount (no Dr/Cr).
                        $formatBalance = function ($balance) {
                            return number_format(abs($balance));
                        };
                        ?>
                    @foreach($dd as $val)

                            <?php $GrandDebit=0 ?>
                            <?php $GrandCredit=0 ?>
                            <?php
                            $ReportData = DB::table("fin_general_ledgers as gl")
                                ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
                                ->join("fin_chart_of_accounts as fca", "fca.id", "=", "gld.coa_id")
                                ->whereBetween("gl.txn_date", array($datef, $datet), "and")
                                ->where("gld.coa_id", $val->id)
                                ->orderBy('fca.parent_code')
                                ->where("voucher_status", 3)
                                ->select(DB::raw(""
                                    . "fca.code, fca.title, fca.parent_code,gl.details as detail, gld.detail as details, gld.debit,gld.credit, gl.txn_date, gl.id, gl.txn_type, gl.txn_series"))
                                // Chronological order so the running balance is meaningful.
                                ->orderBy('gl.txn_date')
                                ->orderBy('gl.id')
                                ->orderBy('gld.id');
                            
                            $opening_bal = DB::table("fin_general_ledgers as gl")
                                ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
                                ->whereDate('gl.txn_date', '<', $datef)
                                ->where("gld.coa_id", $val->id)
                                ->where("voucher_status", 3)
                                ->select(DB::raw("SUM(gld.debit) as debit,SUM(gld.credit) as credit"))->get()->toArray();
                            $opening_balance  = $opening_bal[0]->debit-$opening_bal[0]->credit;
                            $running_balance = (float) $opening_balance;

                           
                            if($opening_balance < 0):
                                 $opening_credit = (abs($opening_balance));
                            else:
                                 $opening_debit = ($opening_balance);
                            endif;
                            ?>
                        <?php if($ReportData->count() > 0): ?>
                            <tr>
                                <td colspan="9"><strong>{{$val->code}} - {{$val->title}}</strong></td>
                            </tr>
                            <tr class="border-blue" style="border-color: #97D35D;border-width: 2px 0px 2px 0px;background-color:gold;border-style: double;">

                                <td colspan="5" class="text-right"><b>Opening Balance</b></td>
                                <td class="text-right"><b>{{number_format($opening_debit)}}</b></td>
                                <td class="text-right"><b>{{number_format($opening_credit)}}</b></td>
                                <td class="text-right" style="white-space:nowrap"><b>{{ $formatBalance($running_balance) }}</b></td>
                                <td></td>
                            </tr>

                        @foreach($ReportData->get() as $Row)

                            <tr class="border-blue">
                                <td>{{ ++$i }}</td>
                                <td>{{ $Row->code }}</td>
                                <td>{{ $Row->title }}</td>
                                <td>{{ App\Http\Controllers\FinGeneralLedgerController::getReference($Row->txn_type,$Row->txn_series,3) }} <br> {{ $Row->details }}</td>
                                <td>{{ date("M j, Y", strtotime($Row->txn_date)) }}</td>
                                <td align="right">{{ number_format($Row->debit) }}</td>
                                <td align="right">{{ number_format($Row->credit) }}</td>
                                <?php $running_balance += $Row->debit - $Row->credit; ?>
                                <td align="right" style="white-space:nowrap">{{ $formatBalance($running_balance) }}</td>
                                <td><a target="_blank" class="dropdown-item" href="{{ route('fin-general-ledgers.show2', $Row->id) }}"><i class="icon-eye"></i>View</a>
                                </td>

                            </tr>

                                <?php $GrandDebit+=$Row->debit ?>
                                <?php $GrandCredit+=$Row->credit ?>
                        @endforeach
                            <tr class="border-blue" style="border-color: #97D35D;border-width: 2px 0px 2px 0px;border-style: double;">
                                <td colspan="5" class="text-right"></td>
                                <td class="text-right"><b>{{ number_format($GrandDebit) }}</b></td>
                                <td class="text-right"><b>{{ number_format($GrandCredit) }}</b></td>
                                <td></td>
                                <td>-</td>
                            </tr>
                            <tr class="border-blue" style="background-color:lightpink;border-color: #97D35D;border-width: 2px 0px 2px 0px;border-style: double;">
                                @php


                                    $credit_cal = $GrandCredit+$opening_credit;
                                    $debit_cal = $GrandDebit+$opening_debit;
                                    $final_cal = $debit_cal-$credit_cal;
                                    if($final_cal < 0 ){

                                        $credit_final = $final_cal;
                                    }else{

                                        $debit_final = $final_cal;
                                    }


                                @endphp

                                <td colspan="5" class="text-right"><b>Closing Balance</b></td>
                                <td class="text-right"><b>{{ number_format($debit_final) }}</b></td>
                                <td class="text-right"><b>{{ number_format(abs($credit_final)) }}</b></td>
                                <td class="text-right" style="white-space:nowrap"><b>{{ $formatBalance($running_balance) }}</b></td>
                                <td>-</td>
                            </tr>
                       <?php endif; $credit_final=0;$debit_final=0;  $opening_debit=0;
                           $opening_credit = 0;
                           $credit_cal = 0;
                           $debit_cal = 0;
                           $final_cal = 0;?>
                    @endforeach




            </tbody>


        </table>
        </div>
      <br><br>

        @endif
    </div>
</div>

@endsection
