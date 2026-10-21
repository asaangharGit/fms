@extends('layouts.master')
@section('page_title', 'Asset Impairment')
@section('content')

    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('asset-registration.assetImpairmentDetail',$detail[0]->id) }}" role="form" enctype="multipart/form-data">
                @csrf

                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Item Name</label>
                                    <input class="form-control" readonly value="{{$detail[0]->code}} - {{$detail[0]->name}}">

                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Amount</label>
                                    <input type="text" name="amount" id="amount" value="" required class="form-control" />
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                    {{ Form::submit('Submit', ['class' => 'btn btn-sm btn-success']) }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </form>
            <div class="table-responsive">
                <table class="table datatable-button-html5-columns table-bordered">
                    <thead class="thead">
                    <tr>
                        <th>No</th>
                        <th>Amount</th>
                        <th>Impairment</th>
                        <th>Created on</th>

                    </tr>
                    </thead>
                    <tbody>
                        <?php $counter=1; ?>
                        @foreach($list as $val)
                        <tr>
                            <td>{{$counter++}}</td>
                            <td>{{$val->amount}}</td>
                            <td>
                                @if($val->type == '-')
                                    <i style="color:red;" class="icon-minus2"></i>
                                @else
                                    <i style="color:green;" class="icon-plus"></i>
                                @endif
                            </td>
                            <td>{{Qs::dateFormat($val->timestamp)}}</td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

@endsection
