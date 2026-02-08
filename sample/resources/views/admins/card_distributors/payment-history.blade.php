@extends ('laraview.layouts.sideNavLayoutForCardDistributors')

@section('title')
    Payment History
@endsection

@section('activeLink')
    @php
        $active_menu = '3';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.card_distributors.sidebar')
@endsection

@section('contentTitle')
    <h3> Payment History </h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <table id="data_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Payment Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($card_distributor_payments as $card_distributor_payment)
                        <tr>
                            <td scope="row">{{ $card_distributor_payment->id }}</td>
                            <td>{{ $card_distributor_payment->amount_paid }}</td>
                            <td>{{ $card_distributor_payment->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
