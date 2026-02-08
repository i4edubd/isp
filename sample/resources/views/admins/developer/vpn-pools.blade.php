@extends ('laraview.layouts.sideNavLayout')

@section('title')
VPN pools
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '12';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection


@section('contentTitle')

<ul class="nav flex-column flex-sm-row">

    <!--New VPN pool-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('vpn-pools.create') }}">
            <i class="fas fa-plus"></i>
            New VPN pool
        </a>
    </li>
    <!--/New VPN pool-->

</ul>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered">

            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Type</th>
                    <th scope="col">subnet</th>
                    <th scope="col">mask</th>
                    <th scope="col">gateway</th>
                    <th scope="col">broadcast</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($pools as $pool )
                <tr>
                    <th scope="row">{{ $pool->id }}</th>
                    <td>{{ $pool->type }}</td>
                    <td>{{ long2ip($pool->subnet) }}</td>
                    <td>{{ $pool->mask }}</td>
                    <td>{{ long2ip($pool->gateway) }}</td>
                    <td>{{ long2ip($pool->broadcast) }}</td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                {{-- delete --}}
                                @can('delete', $pool)
                                <form method="post" action="{{ route('vpn-pools.destroy', ['vpn_pool' => $pool->id]) }}"
                                    onsubmit="return confirm('Are You Sure to Delete!')">
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
