@extends ('laraview.layouts.sideNavLayout')

@section('title')
Group admins
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection


@section('contentTitle')

<form method="GET" action="{{ route('group_admins.index') }}">
    <ul class="nav nav-fill flex-column flex-sm-row">
        <!--New Admin-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('group_admins.create') }}">
                <i class="fas fa-plus"></i>
                New Group Admin
            </a>
        </li>
        <!--/New Admin-->
        <!--Subscription Type-->
        <li class="nav-item">
            <select name="subscription_type" class="custom-select mr-sm-2">
                <option selected value="">Subscription Type...</option>
                <option value="Free">Free</option>
                <option value="Paid">Paid</option>
            </select>
        </li>
        <!--/Subscription Type-->
        <!--Status-->
        <li class="nav-item">
            <select name="status" class="custom-select mr-sm-2">
                <option selected value="">Status...</option>
                <option value="active">active</option>
                <option value="suspended">suspended</option>
                <option value="disabled">disabled</option>
            </select>
        </li>
        <!--/Status-->
        <li class="nav-item">
            <button type="submit" class="btn btn-primary">FILTER</button>
        </li>
    </ul>
</form>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Company</th>
                    <th scope="col">Radius Server</th>
                    <th scope="col">Total User</th>
                    <th scope="col">Status</th>
                    <th scope="col">Subscription Status</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($group_admins as $group_admin)
                <tr class="{{ $group_admin->color }}">
                    <th scope="row">{{ $group_admin->id }}</th>
                    <th>{{ $group_admin->name }}</th>
                    <td>{{ $group_admin->company }}</td>
                    <td>{{ config('database.connections.' . $group_admin->radius_db_connection . '.host') }}</td>
                    <td>{{ $group_admin->group_customers()->count() }}</td>
                    <td>{{ $group_admin->status }}</td>
                    <td>{{ $group_admin->subscription_status }}</td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                <a class="dropdown-item mb-2"
                                    href="{{ route('group_admins.show',['group_admin' => $group_admin->id ]) }}">
                                    Details
                                </a>

                                <a class="dropdown-item mb-2"
                                    href="{{ route('group_admins.edit',['group_admin' => $group_admin->id]) }}">
                                    Edit
                                </a>

                                {{-- --}}
                                @can('suspendSubscription', $group_admin)
                                <a class="dropdown-item mb-2"
                                    href="{{ route('subscription.suspend', ['operator' => $group_admin->id]) }}">
                                    Suspend Subscription
                                </a>
                                @endcan
                                {{-- --}}

                                {{-- --}}
                                @can('activateSubscription', $group_admin)
                                <a class="dropdown-item mb-2"
                                    href="{{ route('subscription.activate', ['operator' => $group_admin->id]) }}">
                                    Activate Subscription
                                </a>
                                @endcan
                                {{-- --}}

                                <a class="dropdown-item mb-2"
                                    href="{{ route('operators.sales_comments.create', ['operator' => $group_admin->id]) }}">
                                    Comments
                                </a>

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
