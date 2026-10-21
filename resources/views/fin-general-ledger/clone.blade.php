@extends('layouts.master')
@section('page_title', 'Clone Voucher')
@section('content')

    @section('content')

        <div class="card card-default">

            <div class="card-body">
                <form method="POST" action="{{ route('fin-general-ledgers.clone') }}" id="myform" role="form" enctype="multipart/form-data">

                    @csrf

                    @include('fin-general-ledger.cloneForm')

                </form>
            </div>
        </div>
    @endsection
