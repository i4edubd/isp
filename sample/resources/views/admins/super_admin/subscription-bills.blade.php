@extends ('laraview.layouts.sideNavLayout')

@section('title')
Subscription Bills
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@section('contentTitle')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('subscription_bills.index') }}" method="get">

    {{-- year --}}
    <div class="form-group col-md-2">
        <select name="year" id="year" class="form-control">
            <option value=''>Year...</option>
            @php
            $start = date(config('app.year_format'));
            $stop = $start - 5;
            @endphp
            @for($i = $start; $i >= $stop; $i--)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select>
    </div>
    {{--year --}}

    {{-- month --}}
    <div class="form-group col-md-2">
        <select name="month" id="month" class="form-control">
            <option value=''>Month...</option>
            <option value='January'>January</option>
            <option value='February'>February</option>
            <option value='March'>March</option>
            <option value='April'>April</option>
            <option value='May'>May</option>
            <option value='June'>June</option>
            <option value='July'>July</option>
            <option value='August'>August</option>
            <option value='September'>September</option>
            <option value='October'>October</option>
            <option value='November'>November</option>
            <option value='December'>December</option>
        </select>
    </div>
    {{--month --}}

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>

{{-- @endFilter --}}

@endsection

@section('content')

<div class="card">

    <!--modal-account-holder -->
    <div class="modal fade" id="modal-account-holder">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-account-holder"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="ModalBody-account-holder">

                    <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>
                    <div class="text-bold pt-2">Loading...</div>
                    <div class="text-bold pt-2">Please Wait</div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /modal-account-holder -->

    <div class="card-body">

        <p> Total Amount: {{ $total }} </p>

        <table id="data_table" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Operator</th>
                    <th scope="col">Customer Count</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Month</th>
                    <th scope="col">Year</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($subscription_bills as $subscription_bill )
                <tr>
                    <th scope="row">{{ $subscription_bill->id }}</th>
                    <td>
                        <a href="#" onclick="showAccountOwnerDetails('{{ $subscription_bill->mgid }}')">
                            {{ $subscription_bill->operator_name }}
                        </a>
                    </td>
                    <td>{{ $subscription_bill->user_count }}</td>
                    <td>{{ $subscription_bill->amount }}</td>
                    <td>{{ $subscription_bill->month }}</td>
                    <td>{{ $subscription_bill->year }}</td>
                    <td class="project-actions text-right">
                        <a class="btn btn-info btn-sm"
                            href="{{ route('subscription_bill.paid.create', ['subscription_bill' => $subscription_bill->id]) }}">
                            <i class="fas fa-money-check"></i>
                            Paid
                        </a>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>

    </div>

</div>

@endsection

@section('pageJs')
<script>
    function showAccountOwnerDetails(operator)
    {
        $.get( "/admin/account-holder-details/" + operator, function( data ) {
            $("#modal-title-account-holder").html("Admin Details");
            $('#modal-account-holder').modal('show');
            $("#ModalBody-account-holder").html(data);
        });
    }

</script>
@endsection
