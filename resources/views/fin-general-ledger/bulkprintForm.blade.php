@extends('layouts.master')
@section('page_title', 'Bulk Print Form')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ url('finance/ledger/bulkprintAll') }}"  role="form" enctype="multipart/form-data">
                @csrf

                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Date from</label>
                                <input required name="datefrom" class="form-control" type="date" value="{{date("Y-m-d")}}"  max="{{date("Y-m-d")}}">
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Date To</label>
                                <input required name="dateto" class="form-control" type="date" value="{{date("Y-m-d")}}" max="{{date("Y-m-d")}}">
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Type</label>
                                <select name="type" required="required" id="type" class="form-control">
                                    <option value="all">All</option>
                                    <option value="1">BPV</option>
                                    <option value="2">BRV</option>
                                    <option value="3">CPV</option>
                                    <option value="4">CRV</option>
                                    <option value="5">JV</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="box-footer mt20 text-right">
                        <button type="submit" class="btn btn-primary"><i class="icon-chevron-right"></i>Generate</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
<script>
    $("#type").select2()
</script>
@endsection
