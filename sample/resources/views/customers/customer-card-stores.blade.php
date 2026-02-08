@extends ('laraview.layouts.topNavLayout')

@section('title')
    Card Stores
@endsection

@section('pageCss')
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('topNavbar')
@endsection

@section('contentTitle')
    @include('customers.logout-nav')
@endsection

@section('content')
    <div class="card">

        {{-- Navigation bar --}}
        <div class="card-header">
            @php
                $active_link = '3';
            @endphp
            @include('customers.nav-links')
        </div>
        {{-- Navigation bar --}}


        <div class="card-body">
            <table id="data_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Store Name</th>
                        <th scope="col">Store Address</th>
                        <th scope="col">Contact Number</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($card_distributors as $card_distributor)
                        <tr>
                            <td>{{ $card_distributor->store_name }}</td>
                            <td>{{ $card_distributor->store_address }}</td>
                            <td>{{ $card_distributor->mobile }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('customers.footer-nav-links')

    </div>
@endsection

@section('pageJs')
@endsection
