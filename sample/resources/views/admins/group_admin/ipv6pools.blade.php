@extends ('laraview.layouts.sideNavLayout')

@section('title')
IPv6pools
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')

<ul class="nav flex-column flex-sm-row">

    <!--New IPv4pool-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('ipv6pools.create') }}">
            <i class="fas fa-plus"></i>
            New IPv6pool
        </a>
    </li>
    <!--/New IPv4pool-->

</ul>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Pool Name</th>
                    <th scope="col">Prefix</th>
                    <th scope="col">Lowest Address</th>
                    <th scope="col">Highest Address</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($pools as $pool )
                <tr>
                    <th scope="row">{{ $pool->id }}</th>
                    <td>{{ $pool->name }}</td>
                    <td>{{ $pool->prefix }}</td>
                    <td>{{ $pool->lowest_address }}</td>
                    <td>{{ $pool->highest_address }}</td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                <a class="dropdown-item"
                                    href="{{ route('ipv6pool_name.edit' , ['ipv6pool' => $pool->id]) }}">
                                    Change Pool Name
                                </a>

                                <a class="dropdown-item"
                                    href="{{ route('ipv6pool_subnet.edit', ['ipv6pool' => $pool->id]) }}">
                                    Change Subnet
                                </a>

                                <a class="dropdown-item"
                                    href="{{ route('ipv6pool_replace.edit', ['ipv6pool' => $pool->id]) }}">
                                    Replace
                                </a>

                                {{-- Delete --}}
                                @can('delete', $pool)
                                <form method="post"
                                    action="{{ route('ipv6pools.destroy', ['ipv6pool' => $pool->id]) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="dropdown-item" type="submit">Delete</button>
                                </form>
                                @endcan
                                {{-- Delete --}}

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
