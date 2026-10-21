@extends('layouts.master')
@section('page_title', 'View Installment Detail')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
        .pendingBadge{
            width: 50px;
            background: #eee30d;
            color:black
        }
        .successBadge{
            width: 50px;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable-button-html5-columns">
                    <thead class="thead">

                    <tr>

                        <th class="text-center">No</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Profit</th>
                        <th class="text-center">Principal</th>
                        <th class="text-center">Recovered Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $counter =1;
                        $total = 0;
                        $principal = 0;
                        $profit = 0;
                    ?>
                    @foreach($data as $val)
                        @php

                        $total += str_replace( ',', '', number_format($val->total_amount) );
                        $principal += str_replace( ',', '', number_format($val->principal) );
                        $profit += str_replace( ',', '', number_format($val->profit) );
                        @endphp
                        <tr>
                            <td class="text-center">{{$counter++}}</td>
                            <td class="text-center">{{$val->loan->name}}</td>
                            <td class="text-center">{{number_format($val->total_amount)}}</td>
                            <td class="text-center">{{number_format($val->principal)}}</td>
                            <td class="text-center">{{number_format($val->profit)}}</td>
                            <td class="text-center">{{\App\Helpers\Qs::dateFormat($val->recovered_date)}}</td>
                            <td class="text-center">
                                @if($val['approved_by'] == 0)
                                    <span class="badge pendingBadge badge-pill badge-warning">Pending</span>
                                @else
                                    <span class="badge successBadge badge-pill badge-success">Approved by --</span>
                                @endif

                            </td>
                            <td class="text-center">
                                <a href="{{ route('fin-recievable.createRecievableJv',['loan_id'=>$val->loan_id ,'installment_id' => $val['id']]) }}" class="btn btn-xs btn-success">Move to GL</a>
                            </td>

                        </tr>
                    @endforeach
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th class="text-center">{{number_format($total)}}</th>
                            <th class="text-center">{{number_format($principal)}}</th>
                            <th class="text-center">{{number_format($profit)}}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
