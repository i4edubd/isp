@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Payments</h1>
    @if(!empty($payments))
        <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; text-align: left;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Amount Paid</th>
                    <th>Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->date }}</td>
                        <td>{{ $payment->amount_paid }}</td>
                        <td>{{ $payment->type }}</td>
                        <td>{{ $payment->pay_status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>You have no payment history.</p>
    @endif
</div>
@endsection
