@extends('layouts.master')
@section('page_title', 'Cheque book')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Fin Checkbook') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('fin-checkbooks.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        <th>Cheque book Name</th>
										<th>Cheque Num Start</th>
										<th>Cheque Num End</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($finCheckbooks as $finCheckbook)
                                        <tr>
                                            <td>{{ ++$i }}</td>

											<td>{{ $finCheckbook->banks->bank_name}}</td>
                                            <td>{{ $finCheckbook->chequebook_name}}</td>
											<td>{{ $finCheckbook->checknum_start }}</td>
											<td>{{ $finCheckbook->checknum_end }}</td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('fin-checkbooks.show',$finCheckbook->id) }}">Show</a>
                                                        <a class="dropdown-item" href="{{ route('fin-checkbooks.edit',$finCheckbook->id) }}">Edit</a>
                                                        <a class="dropdown-item" href="{{ route('fin-checkbooks.destroy',$finCheckbook->id) }}">Delete</a>
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
