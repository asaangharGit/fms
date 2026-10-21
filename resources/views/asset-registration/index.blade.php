@extends('layouts.master')
@section('page_title', 'Asset Registration List')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
              <span id="card_title"></span>
              <div class="float-right">
                <a href="{{ route('asset-registration.create') }}" class="btn btn-primary btn-sm float-right">
                  {{ __('Create New') }}
                </a>
                  <a href="{{ route('asset-registration.renewSession') }}" class="btn btn-danger btn-sm float-right">
                      {{ __('Renew Session') }}
                  </a>
              </div>

        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable-button-html5-columns table-bordered">
                <thead class="thead">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Impairment(-)</th>
                        <th>Total</th>
                        <th>Percent%</th>
                        <th>Per Month Value</th>
                        <th>Total Value</th>
                        <th>Created on</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $counter =1; ?>
                    @foreach($registrations as $val)
                        <tr @if($val->active == 1) style="background: lightgreen" @endif>
                           <td class="text-center">{{$counter++}}</td>
                            <td>{{$val->item->code}} - {{$val->item->name}}</td>
                            <td>{{number_format($val->amount)}}</td>
                            <td>
                                <?php
                                  echo $imp = \App\Models\AssetImpairment::where('asset_register_id',$val->id)->sum('amount')
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $total = $val->amount-$imp;
                                ?>
                            </td>
                            <td>{{$val->percent}}%</td>
                            <td>{{((($val->percent/12)/100)*$total)}}</td>
                            <td>{{((($val->percent/12)/100)*$total*12)}}</td>
                            <td>{{Qs::dateFormat($val->created_on)}}</td>
                            <td><?php echo Qs::statusLabel($val->status)?></td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                         <a class="dropdown-item" href="{{ route('asset-registration.edit',$val->id) }}"><i class="icon-pencil"></i>Edit</a>
                                         <a class="dropdown-item" href="{{ route('asset-registration.destroy',$val->id) }}"><i class="icon-trash"></i>Delete</a>
                                         <a class="dropdown-item" href="{{ route('asset-registration.decreaseForm',$val->id) }}"><i class="icon-minus2"></i>Impairment </a>
                                    </div>
                                </div>
                            </td>
                        </tr>

                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
