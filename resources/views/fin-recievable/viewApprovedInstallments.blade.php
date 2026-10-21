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
                        <th class="text-center">Principal</th>
                        <th class="text-center">Profit</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php $counter =1; $total=0;$profit=0;$principal=0; ?>
                    @foreach($data as $val)
                        @php
                            $total+=str_replace(',', '', number_format($val->total_amount));
                            $principal += str_replace(',', '', number_format($val->principal));
                            $profit += str_replace(',', '', number_format($val->profit));
                        @endphp
                        <tr>
                            <td class="text-center">{{$counter++}}</td>
                            <td class="text-center">{{$val->loan->name}}</td>
                            <td class="text-center">{{number_format($val->total_amount)}}</td>
                            <td class="text-center">{{number_format($val->principal)}}</td>
                            <td class="text-center">{{number_format($val->profit)}}</td>

                        </tr>
                    @endforeach
                    <tfoot>
                    <tr>
                        <td class="text-right" colspan="2"><strong>TOTAL</strong></td>
                        <td class="text-center"><strong>{{number_format($total)}}</strong></td>
                        <td class="text-center"><strong>{{number_format($principal)}}</strong></td>
                        <td class="text-center"><strong>{{number_format($profit)}}</strong></td>
                    </tr>
                    </tfoot>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
