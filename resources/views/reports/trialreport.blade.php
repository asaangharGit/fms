@extends('layouts.master')
@section('page_title', 'Trial Report')
@section('content')
<style>
    tr td ,th{
        padding: 3px !important;

    }
</style>
<div class="card card-default">

    <div class="card-body">

        <form method="POST" action="{{ route('reports.trialreport') }}"  role="form" enctype="multipart/form-data">
            @csrf

            <div class="box box-info padding-1">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                            <label>Date from</label>
                            <input required name="datefrom" class="form-control <?=($errors->has('datefrom') ? ' is-invalid' : '')?>" type="date" value="2020-{{date("m-d")}}" max="{{date("Y-m-d")}}">
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                            <label>Date To</label>
                            <input required name="dateto" class="form-control <?=($errors->has('dateto') ? ' is-invalid' : '')?>" type="date" value="{{date("Y-m-d")}}" max="{{date("Y-m-d")}}">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                            <br>
                            <div class="box-footer mt20">
                                <button type="submit" class="btn btn-primary"><i class="icon-chevron-right"></i>Generate</button>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

            </div>
        </form>

         @if(isset($datef))

        <fieldset>
            <h4>Generated from {{ Qs::dateFormat($datef) }} to {{ Qs::dateFormat($datet) }}</h4>
        </fieldset>
            <div class="table-responsive">
        <table class="table table-bordered datatable-button-html5-columns">
            <thead style="background-color: #97D35D;">
                <tr class="datatable-header center">
                    <th>Main</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Transaction Date</th>
                    <th>Debit</th>
                    <th>Credit</th>

                </tr>
            </thead>
            <tbody>


            @foreach($chart as $key=>$val)

            @php
                $rows = DB::table("fin_general_ledgers as gl")
               ->whereBetween("gl.txn_date", array($datef, $datet), "and")
               ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
               ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
               ->select(DB::raw("
                       fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                      gl.id,gl.txn_date,gld.debit as debit,gld.credit as credit"))
               ->where('parent_code', 'like', $key.'%')
               ->orderBy('fca_l5.code')
               ->get();

            @endphp
            @foreach($rows as $Row)
                    <?php
                    $debt  += $Row->debit;
                    $credit +=$Row->credit;

                    ?>
                <tr class="border-blue">
                    <td><strong>{{$val}}</strong></td>
                    <td>{{ $Row->L5_Code }}</td>
                    <td>{{ $Row->L5_Title }}</td>
                    <td>{{ date("M j, Y", strtotime($Row->txn_date)) }}</td>
                    <td align="right">{{ number_format($Row->debit) }}</td>
                    <td align="right">{{ number_format($Row->credit) }}</td>

                </tr>
            @endforeach

            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right"><strong>TOTAL</strong></th>
                    <th class="text-right"><strong>{{number_format($debt)}}</strong></th>
                    <th class="text-right"><strong>{{number_format($credit)}}</strong></th>
               </tr>
            </tfoot>
        </table><br><br>
     </div>


        @endif
    </div>
</div>

@endsection
