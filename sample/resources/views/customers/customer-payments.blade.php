@extends ('laraview.layouts.topNavLayout')

@section('title')
    Payments
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
                $active_link = '5';
            @endphp
            @include('customers.nav-links')
        </div>
        {{-- Navigation bar --}}

        <div class="card-body">

            <table id="data_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Payment Date</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">TxnID/PIN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->date }}</td>
                            <td>{{ $payment->amount_paid }}</td>
                            <td>{{ $payment->pay_status }}</td>
                            <td>{{ $payment->bank_txnid }}</td>
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
