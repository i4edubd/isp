@extends ('laraview.layouts.sideNavLayout')

@section('title')
Complain Categories
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '20';
$active_link = '2';
@endphp
@endsection

@section('sidebar')

@if (Auth::user()->role == 'group_admin')
@include('admins.group_admin.sidebar')
@endif


@if (Auth::user()->role == 'operator')
@include('admins.operator.sidebar')
@endif

@if (Auth::user()->role == 'sub_operator')
@include('admins.sub_operator.sidebar')
@endif

@if (Auth::user()->role == 'manager')
@include('admins.manager.sidebar')
@endif

@endsection


@section('contentTitle')

<ul class="nav flex-column flex-sm-row ml-4">
    <!--New Category-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('complain_categories.create') }}">
            <i class="fas fa-plus"></i>
            New Category
        </a>
    </li>
    <!--/New Category-->
</ul>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Active complaints</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($complain_categories as $complain_category)
                <tr>
                    <td scope="row">{{ $complain_category->id }}</td>
                    <td>{{ $complain_category->name }}</td>
                    <td>{{ $complain_category->customer_complain()->where('is_active',1)->count() }}</td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                <a class="dropdown-item"
                                    href="{{ route('complain_categories.edit', ['complain_category' => $complain_category->id]) }}">
                                    Edit
                                </a>

                                <form method="post"
                                    action="{{ route('complain_categories.destroy', ['complain_category' => $complain_category->id]) }}"
                                    onsubmit="return confirm('Are you sure to Delete')">
                                    @csrf
                                    @method('delete')
                                    <button class="dropdown-item" type="submit">Delete</button>
                                </form>

                            </div>

                        </div>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    <!--/card body-->

</div>

@endsection

@section('pageJs')
@endsection
