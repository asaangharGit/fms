@extends('layouts.master')
@section('page_title', 'Voucher Management')
@section('content')
    <style>
        .fin_led{
            height: 20px;
            width: 20px;
            cursor: pointer;
        }
        #DataTables_Table_0_filter,#DataTables_Table_0_length{
            display: none;
        }
        .table td, .table th, .table tr{
            padding: 2px;
        }
    </style>
    <div class="card">
        <div class="card-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Voucher Management') }}
                            </span>

                <div class="float-right">
                    <a href="{{ route('fin-general-ledgers.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                        {{ __('Create New') }}
                    </a>

                    <a href="{{ route('fin-general-ledgers.bulkprintForm') }}" class="btn btn-warning btn-sm float-right"  data-placement="left">
                        {{ __('Bulk Print') }}
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
            <form method="POST" action="{{ route('fin-general-ledgers.searchvouchers') }}"  role="form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>Date from</label>
                        <input name="datefrom" class="form-control" type="date" value="{{$parameters['datefrom']}}" max="{{date("Y-m-d")}}">
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>Date To</label>
                        <input name="dateto" class="form-control" type="date" value="{{$parameters['dateto']}}" max="{{date("Y-m-d")}}">
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>Txn type</label>
                        <select class="form-control" id="txn_type" name="txn_type">
                            @foreach( [1 => 'BPV', 'BRV', 'CPV', 'CRV', 'JV'] as $key=>$val)
                                <option @if($parameters['txn_type'] ==$key ) selected  @endif value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>Txn no</label>
                        <input name="txn_no" class="form-control" type="number" value="{{$parameters['txn_series']}}">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <label>Detail</label>
                        <textarea class="form-control" name="detail">{{$parameters['detail']}}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>Cheque Number</label>
                        <input type="number" class="form-control" name="cheque_number" value="{{$parameters['cheque_number']}}">
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>Voucher Status</label>
                        <select class="form-control" name="voucher_status" id="voucher_status">
                            <option value="">Select</option>
                            <option @if($parameters['voucher_status'] ==1 ) selected  @endif value="1">Prepared</option>
                            <option @if($parameters['voucher_status'] ==3 ) selected  @endif value="3">Approved</option>
                        </select>
                        {{--                                [1 => "Prepared", "Checked", "Approved", "Recieved"];--}}
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <br>
                        <button type="submit" class="btn btn-success"><i class="icon-search4"></i>Search</button>
                    </div>
                </div>
            </form>

            <br>
            <br>
            <div class="table-responsive">
                <form method="post" action="{{ route('fin-general-ledgers.bulkApprove') }}">
                    @csrf
                    <table class="table table-bordered datatable-button-html5-columns">
                        <thead class="thead">
                        <tr>
                            <th>No</th>

                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Txn Date</th>
                            <th>Txn Number</th>
                            <th>Purpose</th>
                            <th>Cheq</th>
                            <th>Added By</th>
                            <th>Status</th>
                            <th>Actions</th>
                            @if(in_array(Auth::user()->user_type,Qs::finAuthorityToUser()))
                                <th><button class="btn btn-xs btn-success" type="submit">Approve</button></th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($finGeneralLedgers as $finGeneralLedger)
                            <tr>
                                <td>{{ ++$i }}</td>

                                <td align="right">{{ number_format($finGeneralLedger->debit,0) }}</td>
                                <td align="right">{{ number_format($finGeneralLedger->credit,0) }}</td>
                                <td>{{ \App\Helpers\Qs::dateFormat($finGeneralLedger->txn_date)  }}</td>
                                <td>{{ App\Http\Controllers\FinGeneralLedgerController::getReference($finGeneralLedger->txn_type,$finGeneralLedger->txn_series,3) }}</td>
                                <td>{{ $finGeneralLedger->details }}</td>
                                <td>{{ $finGeneralLedger->cheque_number }}</td>
                                <td>{{ $finGeneralLedger->userinfo->name }}</td>
                                <td><?php echo app('App\Http\Controllers\FinGeneralLedgerController')->getVoucherStatus($finGeneralLedger->voucher_status) ?></td>

                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn-xs btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a target="_blank" class="dropdown-item" href="{{ route('fin-general-ledgers.show', $finGeneralLedger->id) }}"><i class="icon-eye"></i>View</a>
                                            @if(Auth::user()->user_type == 'finance')
                                                @if($finGeneralLedger->voucher_status == 1 )
                                                    <a class="dropdown-item" href="{{ route('fin-general-ledgers.edit',$finGeneralLedger->id) }}">
                                                        <i class="icon-pencil"></i>
                                                        Edit
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('fin-general-ledgers.destroy',$finGeneralLedger->id) }}"><i class="icon-trash"></i>Delete</a>
                                                @endif

                                            @else

                                                <a class="dropdown-item" href="{{ route('fin-general-ledgers.edit',$finGeneralLedger->id) }}"><i class="icon-pencil"></i>Edit</a>
                                                <!--<a class="dropdown-item" href="{{ route('fin-general-ledgers.destroy',$finGeneralLedger->id) }}"><i class="icon-trash"></i>Delete</a>-->
                                                <a class="dropdown-item" href="{{ route('fin-general-ledgers.process', $finGeneralLedger->id) }}"><i class="icon-check"></i>Approve</a>


                                            @endif
                                            <a class="dropdown-item" target="_blank" href="{{ route('fin-general-ledgers.cloneVoucher', $finGeneralLedger->id) }}"><i class="icon-copy2"></i>Reversal</a>


                                        </div>
                                    </div>
                                </td>
                                @if(in_array(Auth::user()->user_type,Qs::finAuthorityToUser()))
                                    <td>
                                        <input class="fin_led form-control" type="checkbox" name="gin_ledger_approve[]" value="{{$finGeneralLedger->id}}">
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>


@endsection
