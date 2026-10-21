@extends('layouts.master')
@section('page_title', 'Update Vendor')
@section('content')

    @section('content')

        <div class="card card-default">

            <div class="card-body">
                <form method="POST" action="{{ route('fin-vendors.update', $vendor->id) }}" role="form"
                      enctype="multipart/form-data">
                    {{ method_field('PATCH') }}
                    @csrf
                    @include('fin-vendors.editForm')

                </form>
            </div>
        </div>
    @endsection
