@extends ('laraview.layouts.sideNavLayout')

@section('title')
Discounts
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
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
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('subscription_discounts.create') }}">
            <i class="fas fa-plus"></i>
            New Discount
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
                    <th scope="col">Discount</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($discounts as $discount )
                <tr>
                    <th scope="row">{{ $discount->id }}</th>
                    <th>{{ $discount->operator_id }}</th>
                    <td>{{ $discount->operator->role }}, {{ $discount->operator->company }}</td>
                    <td>{{ $discount->amount }}</td>
                    <td>

                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <a class="dropdown-item"
                                    href="{{ route('subscription_discounts.edit',['subscription_discount' => $discount->id]) }}">
                                    <i class="fas fa-pencil-alt"></i>
                                    Edit
                                </a>
                                <form method="post"
                                    action="{{ route('subscription_discounts.destroy', ['subscription_discount' => $discount->id]) }}"
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
