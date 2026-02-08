@extends ('laraview.layouts.sideNavLayout')

@section('title')
Affiliate Leads
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '30';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<ul class="nav flex-column flex-sm-row ml-4">
    <!--New Leads-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('affiliate-leads.create') }}">
            <i class="fas fa-plus"></i>
            New Lead (Admin Account)
        </a>
    </li>
    <!--/New Leads-->
</ul>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Support Programme</li>
    <li class="breadcrumb-item active">Leads</li>
</ol>
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
                    <th scope="col">Status</th>
                    <th scope="col">Subscription Status</th>
                </tr>
            </thead>

            <tbody>
                @php
                $i = 0;
                @endphp
                @foreach ($leads as $lead)
                @php
                $i++;
                @endphp
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $lead->name }}</td>
                    <td>{{ $lead->company }}</td>
                    <td>{{ $lead->mobile }}</td>
                    <td>{{ $lead->status }}</td>
                    <td>{{ $lead->subscription_status }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>

@endsection

@section('pageJs')
@endsection