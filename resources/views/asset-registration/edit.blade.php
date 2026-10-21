@extends('layouts.master')
@section('page_title', 'Edit Asset Registration')
@section('content')

    @section('content')

        <div class="card card-default">

            <div class="card-body">
                <form method="POST" action="{{ route('asset-registration.update', $detail[0]->id) }}" role="form"
                      enctype="multipart/form-data">
                    {{ method_field('PATCH') }}
                    @csrf
                    @include('asset-registration.editForm')

                </form>
            </div>
        </div>
    @endsection
