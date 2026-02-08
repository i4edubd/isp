@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Bills</h1>
    @if(!empty($bills))
        <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; text-align: left;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Billing Period</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                    <tr>
                        <td>{{ $bill->id }}</td>
                        <td>{{ $bill->billing_period }}</td>
                        <td>{{ $bill->amount }}</td>
                        <td>{{ $bill->due_date }}</td>
                        <td>{{ $bill->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>You have no bills.</p>
    @endif
</div>
@endsection
