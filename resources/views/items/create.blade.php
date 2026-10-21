@extends('layouts.master')
@section('page_title', 'Create Items')
@section('content')

    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('items.store') }}" role="form" enctype="multipart/form-data">
                @csrf

                @include('items.form')

            </form>
        </div>
    </div>

@endsection
