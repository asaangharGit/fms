@extends('layouts.master')
@section('page_title', 'Renew Asset Session')
@section('content')

    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('asset-registration.renewSessionDetail') }}" role="form" enctype="multipart/form-data">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead">
                        <tr>
                            <th>Item</th>
                            <th>Asset Amount - Year ({{Qs::getCurrentSession()}})</th>
                            <th>Amount Paid - Year ({{Qs::getCurrentSession()}})</th>
                            <th>Balance New Session</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($detail as $val)
                            <tr>
                                <td>{{$val->name}}</td>
                                <td>{{$val->amount}}</td>
                                <td>{{$val->product_count}}</td>
                                <td>{{($val->amount-$val->product_count)}}
                                    <input name="remaining_amount[]" type="hidden" value="{{($val->amount-$val->product_count)}}">
                                    <input name="item_id[]" type="hidden" value="{{$val->item_id}}">
                                    <input name="percent[]" type="hidden" value="{{$val->percent}}">
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        {{ Form::submit('Submit', ['class' => 'btn btn-sm btn-success']) }}
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
