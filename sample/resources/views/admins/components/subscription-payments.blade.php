@section('contentTitle')
<h3> Subscription Payments </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="phpPaging" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Operator </th>
                    <th scope="col">Month </th>
                    <th scope="col">Year </th>
                    <th scope="col">Customer Count</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Pay Status</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($subscription_payments as $subscription_payment )
                <tr>
                    <th scope="row">{{ $subscription_payment->id }}</th>
                    <td>{{ $subscription_payment->operator_name }}</td>
                    <td>{{ $subscription_payment->month }}</td>
                    <td>{{ $subscription_payment->year }}</td>
                    <td>{{ $subscription_payment->user_count }}</td>
                    <td>{{ $subscription_payment->amount_paid }}</td>
                    <td>{{ $subscription_payment->pay_status }}</td>
                    @if ($subscription_payment->pay_status !== 'Successful' &&
                    $subscription_payment->payment_gateway_name
                    !== 'send_money')
                    <td>
                        <a class="btn btn-primary btn-sm"
                            href="{{ route('subscription_payments.recheck', ['subscription_payment' => $subscription_payment->id]) }}">
                            <i class="fas fa-link"></i>
                            Recheck
                        </a>
                    </td>
                    @else
                    <td>
                        @if (Auth::user()->role == 'super_admin')
                        @if ($subscription_payment->pay_status == 'Pending' &&
                        $subscription_payment->payment_gateway_name
                        == 'send_money')
                        {{-- Received --}}
                        <a class="btn btn-primary"
                            href="{{ route('verify-subscription-payment.create', ['action' => 'accept', 'subscription_payment_id' => $subscription_payment->id]) }}"
                            role="button">
                            <i class="far fa-check-circle"></i>
                            Received
                        </a>

                        {{-- Received --}}

                        {{-- Not Received --}}
                        <a class="btn btn-primary"
                            href="{{ route('verify-subscription-payment.create', ['action' => 'reject', 'subscription_payment_id' => $subscription_payment->id]) }}"
                            role="button">
                            <i class="far fa-times-circle"></i>
                            Not Received
                        </a>
                        {{-- Not Received --}}

                        @endif
                        @endif
                    </td>
                    @endif
                </tr>
                @endforeach

            </tbody>
        </table>

    </div>

    <div class="card-footer">
        <div class="row">
            <div class="col-sm-2">
                Total Entries: {{ $subscription_payments->total() }}
            </div>
            <div class="col-sm-6">
                {{ $subscription_payments->withQueryString()->links() }}
            </div>
        </div>
    </div>
    <!--/card-footer-->

</div>

@endsection

@section('pageJs')
@endsection
