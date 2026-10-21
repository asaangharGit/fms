@extends('layouts.master')
@section('page_title', 'Update Items')
@section('content')

    @section('content')

        <div class="card card-default">

            <div class="card-body">
                <form method="POST" action="{{ route('subitems.update', $detail[0]->id) }}" role="form"
                      enctype="multipart/form-data">
                    {{ method_field('PATCH') }}
                    @csrf
                    @include('subitems.editForm')

                </form>
            </div>
        </div>
    @endsection
