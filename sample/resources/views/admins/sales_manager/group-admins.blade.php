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
@include('admins.sales_manager.sidebar')
@endsection


@section('contentTitle')
<h3> Self Registered Admins </h3>
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
                    <th scope="col">Mobile</th>
                    <th scope="col">Email</th>
                    <th scope="col"></th>
                </tr>
            </thead>

            <tbody>

                @foreach ($group_admins as $group_admin)

                <tr class="{{ $group_admin->color }}">
                    <th scope="row">{{ $group_admin->id }}</th>
                    <th>{{ $group_admin->name }}</th>
                    <td>{{ $group_admin->company }}</td>
                    <td>{{ $group_admin->mobile }}</td>
                    <td>{{ $group_admin->email }}</td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

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
