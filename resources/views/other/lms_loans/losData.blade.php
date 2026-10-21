@extends('layouts.master')
@section('page_title', 'Manage Financing')
@section('content')
<style>

    .dot_green {
        height: 25px;
        width: 25px;
        background-color: green;
        border-radius: 50%;
        display: inline-block;
    }
    .dot_red {
        height: 25px;
        width: 25px;
        background-color: red;
        border-radius: 50%;
        display: inline-block;
    }

</style>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage LOS data</h6>

        </div>

        <div class="card-body">

                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Cell</th>
                                <th>Cell Verified</th>
                                <th>Sign up</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $userVal)
                                <tr>
                                    <td>{{$loop->iteration }}</td>
                                    <td>{{$userVal->name }}</td>
                                    <td>{{$userVal->email }}</td>
                                    <td>{{$userVal->cell }}</td>
                                    <td>
                                       @if($userVal->isCellVerfied)
                                            <span class="dot_red"></span>
                                        @else
                                            <span class="dot_green"></span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($userVal->isPostSignupCompleted)
                                            <span class="dot_red"></span>
                                        @else
                                            <span class="dot_green"></span>
                                        @endif

                                       </td>
                                    <td>{{Qs::dateFormat($userVal->createdAt) }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <form method="post" action="{{ route('loans.losdataby_user')  }}">
                                                    @csrf
                                                    <input type="hidden" name="userId" value="{{$userVal->id}}">
                                                    <button type="submit" class="dropdown-item"><i class="icon-eye"></i>View Detail</button>

                                                </form>

                                            </div>
                                        </div>

                                    </td>
                                </tr>

                            @endforeach

                            </tbody>
                        </table>
                    </div>


            </div>





@endsection
<script>export default {
    components: {Interactions}
}
</script>
