@extends('layouts.master')
@section('page_title', 'Create Vendor')
@section('content')

    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('fin-vendors.store') }}" role="form" enctype="multipart/form-data">
                @csrf

                @include('fin-vendors.form')

            </form>
        </div>
    </div>

@endsection
