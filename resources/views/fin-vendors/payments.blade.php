@extends('layouts.master')
@section('page_title', 'Payments List')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
    <div class="card">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable-button-html5-columns table-bordered">
                    <thead class="thead">
                    <tr>
                        <th>No</th>
                        <th>Vendor</th>
                        <th>Inv No.</th>
                        <th>Inv Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Approval Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $counter =1; ?>
                    @foreach($invoice_list as $val)
                        <tr>
                            <td>{{$counter++}}</td>
                            <td>{{$val->vendors->name}}</td>
                            <td>{{$val->invoice_no}}</td>
                            <td>{{\App\Helpers\Qs::dateFormat($val->invoice_date)}}</td>
                            <td>{{\App\Helpers\Qs::dateFormat($val->due_date)}}</td>
                            <td class="text-right">{{$val->invoice_amount}}</td>
                            <td><?php echo Qs::paidStatus($val->id,$val->invoice_amount)?></td>
                            <td><?php echo Qs::invoice_status($val->approval_status)?></td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="{{ route('fin-vendors.ViewVendorInvoice',$val->id) }}"><i class="icon-eye"></i>View</a>
                                       <a class="dropdown-item" href="{{ route('fin-vendors.recievepayment',$val->id) }}"><i class="icon-add"></i>Create Payment</a>

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
