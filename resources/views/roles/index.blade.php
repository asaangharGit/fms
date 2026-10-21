@extends('layouts.master')
@section('page_title', 'Manage Roles')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Roles</h6>
            {!! Qs::getPanelOptions() !!}

        </div>
        <div class="card-body">
                  <table class="table table-bordered">
                        <thead class="thead">
                            <tr>
                                <th>No</th>
                                <th>Role Name</th>
                                <th>Created on</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i =1 ?>
                        @foreach ($roles as $val)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $val->role_name }}</td>
                                <td>{{ $val->created_at }}</td>
                                <td><a class="btn btn-danger btn-xs" href="{{url('/deleteRoles').'/'.$val->id}}"><i class="icon-trash"></i> Delete</a> </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
         </div>


@endsection

