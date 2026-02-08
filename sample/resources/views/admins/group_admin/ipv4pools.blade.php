@extends ('laraview.layouts.sideNavLayout')

@section('title')
IPv4pools
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')

<ul class="nav flex-column flex-sm-row">

    <!--New IPv4pool-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('ipv4pools.create') }}">
            <i class="fas fa-plus"></i>
            New IPv4pool
        </a>
    </li>
    <!--/New IPv4pool-->

    <li class="nav-item ml-4">

        <a class="btn btn-dark" href="{{ route('ipv4pools.index', ['unused' => 'yes']) }}" role="button">
            Unused pools
        </a>

    </li>

</ul>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Pool Name</th>
                    <th scope="col">Addresses</th>
                    <th scope="col">Total IP</th>
                    <th scope="col">Used IP</th>
                    <th scope="col">Free IP</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($pools as $pool )
                <tr>
                    <th scope="row">{{ $pool->id }}</th>
                    <td>{{ $pool->name }}</td>
                    <td>{{ long2ip($pool->subnet).'/'. $pool->mask }}</td>
                    <td>{{ $pool->broadcast - $pool->gateway }}</td>
                    <td>
                        <div class="progress progress-xs">
                            <div class="progress-bar bg-success" style="width: {{ ($pool->used_space / ($pool->broadcast - $pool->gateway)) * 100 }}%"></div>
                        </div>
                        <span class="badge bg-success">{{ $pool->used_space }}</span>
                    </td>
                    <td>
                        <div class="progress progress-xs">
                            <div class="progress-bar bg-info" style="width: {{ (($pool->broadcast - $pool->gateway - $pool->used_space) / ($pool->broadcast - $pool->gateway)) * 100 }}%"></div>
                        </div>
                        <span class="badge bg-info">{{ $pool->broadcast - $pool->gateway - $pool->used_space }}</span>
                    </td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                {{-- Change Pool Name --}}
                                @can('changeName', $pool)
                                <a class="dropdown-item"
                                    href="{{ route('ipv4pool_name.edit', ['ipv4pool' => $pool->id]) }}">
                                    Change Pool Name
                                </a>
                                @endcan
                                {{-- Change Pool Name --}}

                                {{-- Change Subnet --}}
                                <a class="dropdown-item"
                                    href="{{ route('ipv4pool_subnet.edit', ['ipv4pool' => $pool->id]) }}">
                                    Change Subnet
                                </a>
                                {{-- Change Subnet --}}

                                {{-- Replace --}}
                                @can('replace', $pool)
                                <a class="dropdown-item"
                                    href="{{ route('ipv4pool_replace.edit', ['ipv4pool' => $pool->id]) }}">
                                    Replace
                                </a>
                                @endcan
                                {{-- Replace --}}

                                {{-- delete --}}
                                @can('delete', $pool)
                                <form method="post"
                                    action="{{ route('ipv4pools.destroy', ['ipv4pool' => $pool->id]) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="dropdown-item" type="submit">Delete</button>
                                </form>
                                @endcan
                                {{-- delete --}}

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