@extends('layouts.master')
@section('page_title', 'Trial Balance')
@section('content')
    @php

    use DB;
    @endphp
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
                                <input required name="datefrom" class="form-control <?=($errors->has('datefrom') ? ' is-invalid' : '')?>" type="date" value="{{$datef}}" max="{{date("Y-m-d")}}">
                            </div>
                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                <label>Date To</label>
                                <input required name="dateto" class="form-control <?=($errors->has('dateto') ? ' is-invalid' : '')?>" type="date" value="{{$datet}}" max="{{date("Y-m-d")}}">
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



                <div class="text-right">
                    <input class="btn btn-xs btn-primary" type="button" onclick="tableToExcel('finreport', 'Trial Balance')" value="Export to Excel">
                </div>
                <div class="table-responsive" id="finreport">
                    {{Qs::reportHeader($datef,$datet)}}
                    <table class="table table-bordered">

                        <thead>

                        <tr style="background-color: #97D35D;" class="datatable-header center">
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

                             $ret = ($income_prev)+($expense_prev);
                             if($ret < 0){


                                     $retained_credit =$ret;
                                 }
                                 else{
                                     $retained_debit = $ret;

                                 }
                             $total_debit_assets = 0;
                             $total_credit_assets = 0;

//                              if($aaa < 0){
//
//                                  $retained_credit = abs($aaa);
//
//                             }else{
//
//                                 $retained_debit = abs($aaa);
//                             }
                        @endphp
                        @foreach($assets as $val)
                            @php

                                if(($val->debit-$val->credit) < 0):
                                       $credit_assets =  $val->debit-$val->credit;
                                    else:
                                       $dedit_assets =$val->debit-$val->credit;
                                    endif;
                                     $total_debit_assets +=$dedit_assets;
                                     $total_credit_assets += $credit_assets;
                            @endphp
                            <tr>
                                <td><strong>Assets</strong></td>
                                <td>{{$val->L5_Code }} </td>
                                <td>{{$val->L5_Title }}  </td>
                                <td class="text-right">{{number_format(abs($dedit_assets))}}</td>
                                <td class="text-right">{{number_format(abs($credit_assets))}}</td>
                                <td class="text-right">
                                    @if(($val->debit-$val->credit) < 0)
                                        ({{number_format(abs($val->debit-$val->credit)) }})
                                    @else
                                        {{number_format(abs($val->debit-$val->credit)) }}
                                    @endif
                                </td>


                            </tr>
                            @php
                                $credit_assets=0;
                                $dedit_assets=0;
                            @endphp
                        @endforeach



                        @php
                            $total_debit_liabilitiy = 0;
                            $total_credit_liabilitiy = 0;
                        @endphp
                        @foreach($liabilities as $val2)
                            @php

                                if(($val2->debit-$val2->credit) < 0):
                                       $credit_liabality =  $val2->debit-$val2->credit;
                                    else:
                                       $dedit_liability =$val2->debit-$val2->credit;
                                    endif;
                                     $total_debit_liabilitiy +=$dedit_liability;
                                     $total_credit_liabilitiy += $credit_liabality;
                            @endphp
                            <tr>
                                <td><strong>Liability</strong></td>
                                <td>{{$val2->L5_Code }} </td>
                                <td>{{$val2->L5_Title }}  </td>
                                <td class="text-right">{{number_format(abs($dedit_liability))}}</td>
                                <td class="text-right">{{number_format(abs($credit_liabality))}}</td>
                                <td class="text-right">
                                    @if(($val2->debit-$val2->credit) < 0)
                                        ({{number_format(abs($val2->debit-$val2->credit)) }})
                                    @else
                                        {{number_format(abs($val2->debit-$val2->credit)) }}
                                    @endif
                                </td>


                            </tr>
                            @php
                                $dedit_liability=0;
                                $credit_liabality=0;
                            @endphp
                        @endforeach




                        @php
                            $total_debit_equity = 0;
                            $total_credit_equity = 0;
                        @endphp
                        @foreach($equity as $val3)
                            @php

                                if(($val3->debit-$val3->credit) < 0):
                                       $credit_equity =  $val3->debit-$val3->credit;
                                    else:
                                       $dedit_equity =$val3->debit-$val3->credit;
                                    endif;
                                     $total_debit_equity +=$dedit_equity;
                                     $total_credit_equity += $credit_equity;
                            @endphp
                            <tr>
                                <td><strong>Equity</strong></td>
                                <td>{{$val3->L5_Code }} </td>
                                <td>{{$val3->L5_Title }}  </td>
                                <td class="text-right">{{number_format(abs($dedit_equity))}}</td>
                                <td class="text-right">{{number_format(abs($credit_equity))}}</td>
                                <td class="text-right">
                                    @if(($val3->debit-$val3->credit) < 0)
                                        ({{number_format(abs($val3->debit-$val3->credit)) }})
                                    @else
                                        {{number_format(abs($val3->debit-$val3->credit)) }}
                                    @endif
                                </td>


                            </tr>
                            @php
                                $credit_equity=0;
                                $dedit_equity=0;
                            @endphp
                        @endforeach
                        <tr>
                            <th>Retained earning</th>
                            <td>03-01-002-001-0001</td>
                            <td>-</td>
                            <td class="text-right">{{number_format($retained_debit)}}</td>
                            <td class="text-right">{{number_format(abs($retained_credit))}}</td>
                            <td class="text-right">

                                @if(($ret) < 0)
                                    ({{number_format(abs($ret))}})
                                @else
                                    {{number_format($ret)}}


                                @endif
                            </td>
                        </tr>

                        @php
                            $total_debit_expense = 0;
                            $total_credit_expense = 0;
                        @endphp
                        @foreach($expense as $val4)
                            @php

                                if(($val4->debit-$val4->credit) < 0):
                                       $credit_expense =  $val4->debit-$val4->credit;
                                    else:
                                       $dedit_expense =$val4->debit-$val4->credit;
                                    endif;
                                     $total_debit_expense +=$dedit_expense;
                                     $total_credit_expense += $credit_expense;
                            @endphp
                            <tr>
                                <td><strong>Expense</strong></td>
                                <td>{{$val4->L5_Code }} </td>
                                <td>{{$val4->L5_Title }}  </td>
                                <td class="text-right">{{number_format(abs($dedit_expense))}}</td>
                                <td class="text-right">{{number_format(abs($credit_expense))}}</td>
                                <td class="text-right">
                                    @if(($val4->debit-$val4->credit) < 0)
                                        ({{number_format(abs($val4->debit-$val4->credit)) }})
                                    @else
                                        {{number_format(abs($val4->debit-$val4->credit)) }}
                                    @endif
                                </td>


                            </tr>
                            @php
                                $credit_expense=0;
                                $dedit_expense=0;
                            @endphp
                        @endforeach


                        @php
                            $total_debit_income = 0;
                            $total_credit_income = 0;
                        @endphp
                        @foreach($income as $val5)
                            @php

                                if(($val5->debit-$val5->credit) < 0):
                                       $credit_income =  $val5->debit-$val5->credit;
                                    else:
                                       $dedit_income =   $val5->debit-$val5->credit;
                                    endif;
                                     $total_debit_income +=$dedit_income;
                                     $total_credit_income += $credit_income;
                            @endphp
                            <tr>
                                <td><strong>Income</strong></td>
                                <td>{{$val5->L5_Code }} </td>
                                <td>{{$val5->L5_Title }}  </td>
                                <td class="text-right">{{number_format(abs($dedit_income))}}</td>
                                <td class="text-right">{{number_format(abs($credit_income))}}</td>
                                <td class="text-right">
                                    @if(($val5->debit-$val5->credit) < 0)
                                        ({{number_format(abs($val5->debit-$val5->credit)) }})
                                    @else
                                        {{number_format(abs($val5->debit-$val5->credit)) }}
                                    @endif
                                </td>


                            </tr>
                            @php
                                $dedit_income=0;
                                $credit_income=0;
                            @endphp
                        @endforeach
                         @php

                            $grand_debit = $retained_debit+$total_debit_assets+$total_debit_liabilitiy+$total_debit_equity+$total_debit_expense+$total_debit_income;
                             $grand_credit = abs($retained_credit)+abs($total_credit_assets)+abs($total_credit_liabilitiy)+abs($total_credit_equity)+abs($total_credit_expense)+abs($total_credit_income);

                     @endphp
                        </tbody>
                        <tfoot>

                        <tr>
                            <th colspan="3" class="text-right"><strong>TOTAL</strong></th>
                            <th class="text-right"><strong>{{number_format($grand_debit)}}</strong></th>
                            <th class="text-right"><strong>{{number_format(abs($grand_credit))}}</strong></th>
                            <th class="text-right"><strong>{{number_format(abs(abs($grand_debit)-abs($grand_credit)))}}</strong></th>
                        </tr>
                        </tfoot>
                    </table><br><br>
                </div>


            @endif
        </div>
    </div>
@php

DB::Table('fin_info')->insert(['debit'=>$grand_debit,'credit'=>abs($grand_credit)]);

@endphp
@endsection

