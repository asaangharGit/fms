@extends('layouts.master')
@section('page_title', 'Create Asset Registration')
@section('content')

    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('asset-registration.store') }}" role="form" enctype="multipart/form-data">
                @csrf

                @include('asset-registration.form')

            </form>
        </div>
    </div>

@endsection
