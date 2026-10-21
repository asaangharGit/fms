@extends('layouts.master')
@section('page_title', 'Payment List')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
        .mybadge {
            width: 33px;
            background: #eee30d;
            color:black;
            font-size: 14px;
        }
        .expBadge{
            width: 200px;
            font-size: 14px;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable-button-html5-columns table-bordered">
                    <thead class="thead">
                    <tr>
                        <th>No</th>
                        <th class="text-center">Code</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Principal</th>
                        <th>Profit</th>
                        <th style="width: 10%" class="text-center">Disbursed date</th>
                        <th class="text-center">New Installments</th>
                        <th class="text-center">Installment Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $counter =1; ?>
                    @foreach($loanlist as $val)

                        <tr>
                            <td>{{$counter++}}</td>
                            <td class="text-center">{{$val->account_no}}</td>
                            <td>{{$val->name}}</td>
                            <td>{{number_format($val->total_amount)}}</td>
                            <td>{{number_format($val->principal)}}</td>
                            <td>{{number_format($val->profit)}}</td>
                            <td class="text-center">{{\App\Helpers\Qs::dateFormat($val->disbursed_date)}}</td>
                            <td class="text-center">
                                <span class="badge mybadge badge-pill badge-danger">{{$val->installments->count()-$val->installments->where('approved_by','>',0)->count()}}</span>
                            </td>
                            <td class="text-center">
                                @php
                                     if(\App\Helpers\Qs::installmentDateExpiry($val->upcoming_installment_payment) == 0 ):
                                         echo \App\Helpers\Qs::installmentDateExpiry($val->upcoming_installment_date);
                                     endif;
                                @endphp
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="{{ route('fin-recievable.viewInstallmentDetail',['loan_id' => $val['id']]) }}">View Installments</a>
                                        <a class="dropdown-item" href="{{ route('fin-recievable.viewLoanStatement',['loan_id' => $val['id']]) }}">View Statement</a>
                                        <a class="dropdown-item" href="{{ route('fin-recievable.viewApprovedInstallments',['loan_id' => $val['id']]) }}">View Approved Installments</a>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
