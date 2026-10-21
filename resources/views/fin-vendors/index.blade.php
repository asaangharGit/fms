@extends('layouts.master')
@section('page_title', 'Vendors List')
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
                        <th>Name</th>
                        <th>Ntn</th>
                        <th>Contact</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Created on</th>
                        <th class="text-center">Status</th>

                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $counter =1; ?>
                    @foreach($vendors as $val)
                        <tr>
                           <td>{{$counter++}}</td>
                            <td>{{$val->name}}</td>
                            <td>{{$val->ntn}}</td>
                            <td>{{$val->contact}}</td>
                            <td>{{DB::Table('cities')->where('id',$val->city_id)->value('name')}}</td>
                            <td>{{$val->address}}</td>
                            <td>{{Qs::dateFormat($val->timestamp)}}</td>
                            <td><?php echo Qs::statusLabel($val->status)?></td>

                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                         <a class="dropdown-item" href="{{ route('fin-vendors.edit',$val->id) }}"><i class="icon-pencil"></i>Edit</a>
                                         <a class="dropdown-item" href="{{ route('fin-vendors.destroy',$val->id) }}"><i class="icon-trash"></i>Delete</a>
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
