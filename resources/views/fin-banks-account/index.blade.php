@extends('layouts.master')
@section('page_title', 'Fin Banks Account')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Fin Banks Account') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('fin-banks-accounts.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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

										<th>Bank Name</th>
										<th>Bank Account</th>


                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($finBanksAccounts as $finBanksAccount)
                                        <tr>
                                            <td>{{ ++$i }}</td>

											<td>{{ $finBanksAccount->bank_name }}</td>
											<td>{{ $finBanksAccount->bank_account }}</td>


                                            <td>


                                                <div class="dropdown">
                                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('fin-banks-accounts.show',$finBanksAccount->id) }}">Show</a>
                                                        <a class="dropdown-item" href="{{ route('fin-banks-accounts.edit',$finBanksAccount->id) }}">Edit</a>
                                                        <a class="dropdown-item" href="{{ route('fin-banks-accounts.destroy',$finBanksAccount->id) }}">Delete</a>


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
