@extends('layouts.master')
@section('page_title', 'Update Items')
@section('content')

    @section('content')

        <div class="card card-default">

            <div class="card-body">
                <form method="POST" action="{{ route('items.update', $assets->id) }}" role="form"
                      enctype="multipart/form-data">
                    {{ method_field('PATCH') }}
                    @csrf
                    @include('items.editForm')

                </form>
            </div>
        </div>
    @endsection
