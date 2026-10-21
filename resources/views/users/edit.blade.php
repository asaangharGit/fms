@extends('layouts.master')
@section('page_title', 'Edit User')
@section('content')


                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update User</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ url('updateUsers/'.$users[0]->id) }}"  role="form" enctype="multipart/form-data">
                            <input type="hidden" name="password_old" value="{{$users[0]->password}}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-lg-3 col-form-label font-weight-semibold">Name</label>
                                <div class="col-lg-9">
                                    <input required name="name" class="form-control" type="text" value="{{$users[0]->name}}">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="email" class="col-lg-3 col-form-label font-weight-semibold">Email </label>
                                <div class="col-lg-9">
                                    <input required name="email" type="email" class="form-control" value="{{$users[0]->email}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-lg-3 col-form-label font-weight-semibold">Password </label>
                                <div class="col-lg-9">
                                    <input name="password" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-lg-3 col-form-label font-weight-semibold">Roles </label>
                                <div class="col-lg-9">
                                    <select required class="form-control" name="role_id">
                                        <option selected>Select</option>
                                        @foreach($roles as $val)
                                            <option @if($users[0]->roleId  == $val->id) selected @endif value="{{$val->id}}">{{$val->role_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="text-right">
                                <button type="submit" class="btn btn-success">Submit form <i class="icon-paperplane ml-2"></i></button>
                            </div>

                        </form>
                    </div>
                </div>
@endsection
