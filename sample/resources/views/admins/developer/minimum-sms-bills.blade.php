@extends ('laraview.layouts.sideNavLayout')

@section('title')
Minimum SMS Bill
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@section('contentTitle')

<ul class="nav flex-column flex-sm-row ml-4">
    <!--New Discount-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('minimum_sms_bills.create') }}">
            <i class="fas fa-plus"></i>
            New Setting
        </a>
    </li>
    <!--/New Discount-->
</ul>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Operator ID</th>
                    <th scope="col">Operator</th>
                    <th scope="col">Amount</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($minimum_bills as $minimum_bill )
                <tr>
                    <th scope="row">{{ $minimum_bill->id }}</th>
                    <td>{{ $minimum_bill->operator_id }}</td>
                    <td>{{ $minimum_bill->operator->role }}, {{ $minimum_bill->operator->company }}</td>
                    <td>{{ $minimum_bill->amount }}</td>
                    <td>

                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <a class="dropdown-item"
                                    href="{{ route('minimum_sms_bills.edit', ['minimum_sms_bill' => $minimum_bill->id]) }}">
                                    <i class="fas fa-pencil-alt"></i>
                                    Edit
                                </a>
                                <form method="post"
                                    action="{{ route('minimum_sms_bills.destroy', ['minimum_sms_bill' => $minimum_bill->id]) }}"
                                    onsubmit="return confirm('Are you sure to Delete')">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
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

@section('pageJs')
@endsection
