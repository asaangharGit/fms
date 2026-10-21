@extends('layouts.master')
@section('page_title', 'Edit Voucher')
@section('content')

@section('content')

<div class="card card-default">
    <div class="card-body">
        <form method="POST" action="{{ route('fin-general-ledgers.update', $finGeneralLedger->id) }}"  role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf

            @include('fin-general-ledger.editForm')

        </form>
    </div>
</div>
@endsection
