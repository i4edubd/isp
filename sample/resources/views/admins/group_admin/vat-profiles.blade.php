@extends ('laraview.layouts.sideNavLayout')

@section('title')
VAT Profiles
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '40';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<ul class="nav flex-column flex-sm-row">
    <li class="nav-item mr-2">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('vat_profiles.create') }}">
            <i class="fas fa-plus"></i>
            New VAT Profile
        </a>
    </li>
</ul>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">VAT</li>
    <li class="breadcrumb-item active">Profiles</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Identification Number</th>
                    <th scope="col">Rate</th>
                    <th scope="col">Status</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($profiles as $profile )
                <tr>
                    <th scope="row">{{ $profile->id }}</th>
                    <td>{{ $profile->description }}</td>
                    <td>{{ $profile->identification_number }}</td>
                    <td>{{ $profile->rate }}</td>
                    <td>{{ $profile->status }}</td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                {{-- Edit --}}
                                @can('update', $profile)
                                <a class="dropdown-item"
                                    href="{{ route('vat_profiles.edit', ['vat_profile' => $profile]) }}">
                                    Edit
                                </a>
                                @endcan
                                {{-- Edit --}}

                                {{-- Delete --}}
                                @can('delete', $profile)
                                <form method="post"
                                    action="{{ route('vat_profiles.destroy', ['vat_profile' => $profile]) }}"
                                    onsubmit="return confirm('Are You Sure to Delete?')">
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