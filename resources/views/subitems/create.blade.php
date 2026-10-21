@extends('layouts.master')
@section('page_title', 'Create Sub Items')
@section('content')

    <div class="card card-default">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                    <a class="btn btn-primary btn-sm float-right" href="{{route('items.create')}}">Create Items</a>
                </div>
            </div>
            <form method="POST" action="{{ route('subitems.store') }}" role="form" enctype="multipart/form-data">
                @csrf

                @include('subitems.form')

            </form>
        </div>
    </div>

@endsection
