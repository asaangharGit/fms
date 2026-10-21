@extends('layouts.master')
@section('page_title', 'Users List')
@section('content')

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table datatable-button-html5-columns">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Username</th>
										<th>Email</th>
                                        <th>Role</th>
                                        <th>Created on</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=0; ?>
                                    @foreach ($users as $val)
                                        <tr>
                                            <td>{{ ++$i }}</td>

											<td>{{ $val->name }}</td>
											<td>{{ $val->username }}</td>
                                            <td>{{ $val->email }}</td>
                                            <td>{{ $val->role_name }}</td>
											<td>{{ $val->created_at }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                      Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{url('editUsers/'.$val->id)}}">Edit</a>
                                                        <a class="dropdown-item" href="{{url('deleteUsers/'.$val->id)}}">Delete</a>

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
