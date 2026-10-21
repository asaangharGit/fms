@extends('layouts.master')
@section('page_title', 'View Loan Statement Form')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">View loan Statement</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('fin-recievable.viewLoanStatementDetail') }}" id="myform" role="form" enctype="multipart/form-data">
                @csrf

                <div class="box box-info padding-1 iamount">
                    <input type="hidden" value="{{ app('request')->input('loan_id') }}" name="loan_id">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>From Date</label>
                                <input required class="form-control" name="from_date" type="date" value="2020-<?=date('m-d')?>" max="<?=date('Y-m-d')?>">
                            </div>
                            <div class="col-md-4">
                                <label>Till Date</label>
                                <input required class="form-control" name="till_date" type="date" value="<?=date('Y-m-d')?>" max="<?=date('Y-m-d')?>">
                            </div>
                            <div class="col-md-4">
                                <br>
                                <button class="btn btn-xs btn-success" type="submit">Generate</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
