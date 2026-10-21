@extends('layouts.master')
@section('page_title', 'Fin General Ledger')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Fin General Ledger') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('fin-general-ledgers.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
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
                            <table class="table datatable-button-html5-columns">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Txn Date</th>
                                        <th>Txn Number</th>
                                        <th>Purpose</th>
                                        <th>Added By</th>
                                        <th>Status</th>
                                        <th>Actions</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($finGeneralLedgers as $finGeneralLedger)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td align="right">{{ number_format($finGeneralLedger->debit,0) }}</td>
                                            <td align="right">{{ number_format($finGeneralLedger->credit,0) }}</td>
                                            <td>{{ date("j M Y", strtotime($finGeneralLedger->txn_date)) }}</td>
                                            <td>{{ App\Http\Controllers\FinGeneralLedgerController::getReference($finGeneralLedger->txn_type,$finGeneralLedger->txn_series,3) }}</td>
                                            <td>{{ $finGeneralLedger->details }}</td>
                                            <td>{{ $finGeneralLedger->userinfo->name }}</td>

                                            <td><?php echo app('App\Http\Controllers\FinGeneralLedgerController')->getVoucherStatus($finGeneralLedger->voucher_status) ?></td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('fin-general-ledgers.show', $finGeneralLedger->id) }}"><i class="icon-eye"></i>View</a>
                                                        @if(in_array(Auth::user()->user_type,Qs::finAuthorityToUser()))
                                                            <a class="dropdown-item" href="{{ route('fin-general-ledgers.edit',$finGeneralLedger->id) }}"><i class="icon-pencil"></i>Edit</a>
                                                            <a class="dropdown-item" href="{{ route('fin-general-ledgers.destroy',$finGeneralLedger->id) }}"><i class="icon-trash"></i>Delete</a>
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
