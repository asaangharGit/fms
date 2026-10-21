@extends('layouts.master')
@section('page_title', 'Invoice List')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
    <div class="card">
        <div class="card-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span id="card_title"></span>
                <div class="float-right">
                    <a href="{{ route('fin-vendors.createVendorInvoice') }}" class="btn btn-primary btn-sm float-right"  data-placement="right">
                        {{ __('Create Invoice') }}
                    </a>

                </div>

            </div>
        </div>
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
                            <td><?php echo Qs::statusLabel($val->status)?></td>
                            <td><?php echo Qs::invoice_status($val->approval_status)?></td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="{{ route('fin-vendors.ViewVendorInvoice',$val->id) }}"><i class="icon-eye"></i>View</a>
                                        @if($val->approval_status == 1)
                                        <a class="dropdown-item" href="{{ route('fin-vendors.VendorInvoiceEditForm',$val->id) }}"><i class="icon-pencil"></i>Edit</a>
                                        <a class="dropdown-item" href="{{ route('fin-vendors.VendorInvoiceDestroy',$val->id) }}"><i class="icon-trash"></i>Delete</a>
                                        @endif

                                        @if(in_array(Auth::user()->user_type,Qs::finAuthorityToUser()) && $val->approval_status == 1)
                                        <a class="dropdown-item" onclick="confirmApprove(<?=$val->id?>)" ><i class="icon-trash"></i>Approve</a>
                                        @endif
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
