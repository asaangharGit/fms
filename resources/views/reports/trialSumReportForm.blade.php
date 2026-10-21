@extends('layouts.master')
@section('page_title', 'Trial Balance')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
    <div class="card card-default">

        <div class="card-body">

            <form method="POST" action="{{ route('reports.trialSumReportDetail') }}"  role="form" enctype="multipart/form-data">
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
                        {{Qs::reportHeader($datef,$datet)}}
                        <thead style="background-color: #97D35D;">
                        <tr class="datatable-header center">
                            <th>Main head</th>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Net</th>



                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $total_debit = 0;
                            $total_credit = 0;
                        @endphp
                        @foreach($ReportData as $val)
                            @php

                                if(($val->debit-$val->credit) < 0):
                                       $credit =  $val->debit-$val->credit;
                                    else:
                                       $dedit =$val->debit-$val->credit;
                                    endif;
                                     $total_debit +=$dedit;
                                $total_credit += $credit;
                            @endphp
                            <tr>
                                <td>
                                    @if($val->L5_Code[0].$val->L5_Code[1] == 01)
                                        <strong>Assets</strong>
                                    @elseif($val->L5_Code[0].$val->L5_Code[1] == 02)
                                        <strong>Liabalities</strong>
                                    @elseif($val->L5_Code[0].$val->L5_Code[1] == 03)
                                        <strong>Equity</strong>
                                    @elseif($val->L5_Code[0].$val->L5_Code[1] == 04)
                                        <strong>Expenses</strong>
                                    @elseif($val->L5_Code[0].$val->L5_Code[1] == 05)
                                        <strong>Income</strong>
                                    @endif
                                </td>
                                <td>{{$val->L5_Code }} </td>
                                <td>{{$val->L5_Title }}  </td>
                                <td class="text-right">{{number_format(abs($dedit))}}</td>
                                <td class="text-right">{{number_format(abs($credit))}}</td>
                                <td class="text-right">
                                    @if(($val->debit-$val->credit) < 0)
                                    ({{number_format(abs($val->debit-$val->credit)) }})
                                    @else
                                        {{number_format(abs($val->debit-$val->credit)) }}
                                    @endif
                                </td>


                            </tr>
                            @php
                            $dedit=0;
                            $credit=0;
                            @endphp
                        @endforeach

                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3" class="text-right"><strong>TOTAL</strong></th>
                            <th class="text-right"><strong>{{number_format($total_debit)}}</strong></th>
                            <th class="text-right"><strong>{{number_format(abs($total_credit))}}</strong></th>
                            <th class="text-right"><strong>{{number_format(abs(abs($total_debit)-abs($total_credit)))}}</strong></th>
                        </tr>
                        </tfoot>
                    </table><br><br>
                </div>


            @endif
        </div>
    </div>

@endsection
