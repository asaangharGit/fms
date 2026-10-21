@extends('layouts.master')
@section('page_title', 'Manage Financing Details')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Process Financing Scheduling</h6>
        {!! Qs::getPanelOptions() !!}


    </div>

    <div class="card-body">
        <div class="tab-content">



            <div class="tab-pane fade show active" >
                <table class="table datatable-button-html5-columns">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Group</th>
                            <th>Product</th>
                            <th>Office</th>
                            <th>Principle</th>
                            <th>Profit</th>
                            <th>Disb.Date</th>
                            <th>Status</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tt_records as $mc)
                        <tr>
                            <td><a href="{{ route('loans.menu', $mc->id) }}" class="dropdown-item">{{ $loop->iteration }}</a></td>
                            <td><a href="{{ route('loans.menu', $mc->id) }}" class="dropdown-item">{{ $mc->loan_borrower->fname }}</a></td>
                            <td><a href="{{ route('loans.menu', $mc->id) }}" class="dropdown-item">{{ $mc->loan_group->name }}</a></td>
                            <td>{{ $mc->loantype->name }}</td>
                            <td>{{ $mc->loan_office->name }}</td>
                            <td>{{ number_format($mc->total_amount_pr,0) }}</td>
                            <td>{{ number_format($mc->total_amount_mu,0) }}</td>
                            <td>{{ date("d M Y",strtotime($mc->disb_date)) }}</td>
                            <td><?php echo app('App\Http\Controllers\LoansController')->getStatus($mc->loan_status_id) ?></td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="{{route('ttr.showloan', $mc->id) }}"><i class="icon-eye"></i>View Profile</a>
                                        <a class="dropdown-item" href="{{  route('loans.menu', $mc->id) }}"><i class="icon-cash"></i> Generate Schedule</a>
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


    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

</div>

{{--TimeTable Ends--}}

@endsection
