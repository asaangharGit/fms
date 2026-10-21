@extends('layouts.master')
@section('page_title', 'Fin Chart Of Account')
@section('content')
    <style>
        tr td ,th{
            padding: 3px !important;

        }
    </style>
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">

            <span id="card_title">
                {{ __('Fin Chart Of Account') }}
            </span>

{{--            <div class="float-right">--}}
{{--                <a href="{{ route('fin-chart-of-accounts.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">--}}
{{--                    {{ __('Create New') }}--}}
{{--                </a>--}}
{{--            </div>--}}
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif

    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable-button-html5-columns">
                <thead class="thead">
                    <tr>
                        <th>No</th>
                        <th>Title</th>
                        <th>Level</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($finChartOfAccounts as $finChartOfAccount)
                    <tr style="background: @if($finChartOfAccount->level == 'L1') #64c464
                        @elseif($finChartOfAccount->level == 'L2')
                        #9aed9a
                        @elseif($finChartOfAccount->level == 'L3')
                        #acedac
                        @elseif($finChartOfAccount->level == 'L4')
                        #b9f1b9
                        @else
                        #ccf4cc
                      @endif " >
                        <td>{{ ++$i }}</td>

                        <td>
                            @if($finChartOfAccount->level == 'L1')
                                <span style="font-weight: 800"> {{ $finChartOfAccount->code.' - '.$finChartOfAccount->title }}</span>
                            @elseif($finChartOfAccount->level == 'L2')
                                <span style="padding-left: 15px"></span>
                                <span style="font-weight: 500">{{ $finChartOfAccount->code.' - '.$finChartOfAccount->title }}</span>
                            @elseif($finChartOfAccount->level == 'L3')
                                <span style="padding-left: 30px"></span>
                                <span style="font-weight: 400">{{ $finChartOfAccount->code.' - '.$finChartOfAccount->title }}</span>
                            @elseif($finChartOfAccount->level == 'L4')
                                <span style="padding-left: 45px"></span>
                                {{ $finChartOfAccount->code.' - '.$finChartOfAccount->title }}
                            @elseif($finChartOfAccount->level == 'L5')
                                <span style="padding-left: 60px"></span>
                                {{ $finChartOfAccount->code.' - '.$finChartOfAccount->title }}
                            @endif

                        </td>
                        <td>{{$finChartOfAccount->level}}</td>
                        <td>{{ date("j M Y H:i A", strtotime($finChartOfAccount->created_at))  }}</td>

                        <td>
                              <div class="dropdown">
                                <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="{{ route('fin-chart-of-accounts.show',$finChartOfAccount->id) }}">Show</a>
                                    <a class="dropdown-item" href="{{ route('fin-chart-of-accounts.edit',$finChartOfAccount->id) }}">Edit</a>
{{--                                    <a class="dropdown-item" href="{{ route('fin-chart-of-accounts.destroy',$finChartOfAccount->id) }}">Delete</a>--}}


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
