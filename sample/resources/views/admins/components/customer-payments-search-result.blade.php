<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name <br> Username</th>
            <th scope="col">Mobile</th>
            <th scope="col">Purpose and <br> Validity </th>
            <th scope="col" style="width: 30%">Amount</th>
            <th scope="col">Payment Gateway</th>
            <th scope="col">Date</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>

        @foreach ($payments as $payment)
            <tr>
                <td scope="row">{{ $payment->id }}</td>
                <td>
                    {{ $payment->name }} <br>
                    {{ $payment->username }}
                </td>
                <td>
                    <a href="#" onclick="showCustomerDetails('{{ $payment->customer_id }}')">
                        {{ $payment->mobile }}
                    </a>
                </td>
                <td>{{ $payment->purpose }} <br> ({{ $payment->validity_period }})</td>
                <td>
                    <div class="row">
                        <div class="col-sm">
                            Amount Paid: {{ $payment->amount_paid }} <br>
                            Service Charge: {{ $payment->transaction_fee }} <br>
                            VAT: {{ $payment->vat_paid }}
                        </div>
                        <div class="col-sm">
                            Store Amount: {{ $payment->store_amount }} <br>
                            @if (Auth::user()->show_payment_breakdown == 'yes')
                                1st party: {{ $payment->first_party }} <br>
                                2nd party: {{ $payment->second_party }} <br>
                                3rd party: {{ $payment->third_party }} <br>
                            @endif
                        </div>
                    </div>
                </td>
                @if ($payment->type == 'RechargeCard')
                    <td>{{ $payment->recharge_card->distributor->name }}</td>
                @else
                    <td>{{ $payment->payment_gateway_name }}</td>
                @endif
                <td>{{ $payment->created_at }}</td>
                <td>

                    @if (Auth::user()->can('update', $payment) || Auth::user()->can('delete', $payment))
                        <div class="btn-group" role="group">

                            <button id="btnGroupActionsOnCustomer" type="button" class="btn btn-danger dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupActionsOnCustomer">
                                {{-- --}}
                                @can('update', $payment)
                                    <a class="dropdown-item"
                                        href="{{ route('customer_payments.edit.create', ['customer_payment' => $payment->id]) }}">
                                        Edit
                                    </a>
                                @endcan
                                {{-- --}}
                                @can('delete', $payment)
                                    <a class="dropdown-item"
                                        href="{{ route('customer_payments.destroy.create', ['customer_payment' => $payment->id]) }}">
                                        Delete
                                    </a>
                                @endcan
                                {{-- --}}
                            </div>

                        </div>
                    @endif

                </td>

            </tr>
        @endforeach

    </tbody>

</table>
