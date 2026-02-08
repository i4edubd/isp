@extends ('laraview.layouts.sideNavLayout')

@section('title')
Affiliate Sales
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '30';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3>Account Balance : {{ $account->balance }} {{ getCurrency(Auth::user()->id) }}</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Support Programme</li>
    <li class="breadcrumb-item active">Sales</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            <a href="{{ route('accounts.receivable') }}"> <i class="fas fa-eye"></i> Balance Sheet</a>
        </h3>
    </div>

    <div class="card-body">

        <table id="data_table" class="table table-hover">

            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Lead</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Old Balance</th>
                    <th scope="col">New Balance</th>
                    <th scope="col">Date</th>
                </tr>
            </thead>

            <tbody>
                @php
                $i = 0;
                @endphp
                @foreach ($sales as $sale)
                @php
                $i++;
                @endphp
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $sale->name }}</td>
                    <td>{{ $sale->amount }}</td>
                    <td>{{ $sale->old_balance }}</td>
                    <td>{{ $sale->new_balance }}</td>
                    <td>{{ $sale->created_at }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>

@endsection

@section('pageJs')
@endsection