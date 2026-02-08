@extends ('laraview.layouts.sideNavLayout')

@section('title')
Max Subscription Payments
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '9';
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
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('max_subscription_payments.create') }}">
            <i class="fas fa-plus"></i>
            New Max Subscription Payment
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
                    <th scope="col">Max Payment</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($max_payments as $max_payment )
                <tr>
                    <th scope="row">{{ $max_payment->id }}</th>
                    <td>{{ $max_payment->operator_id }}</td>
                    <td>{{ $max_payment->operator->role }}, {{ $max_payment->operator->company }}</td>
                    <td>{{ $max_payment->amount }}</td>
                    <td>

                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <a class="dropdown-item"
                                    href="{{ route('max_subscription_payments.edit', ['max_subscription_payment' => $max_payment->id]) }}">
                                    <i class="fas fa-pencil-alt"></i>
                                    Edit
                                </a>
                                <form method="post"
                                    action="{{ route('max_subscription_payments.destroy', ['max_subscription_payment' => $max_payment->id]) }}"
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
