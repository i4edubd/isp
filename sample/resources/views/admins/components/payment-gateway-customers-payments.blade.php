@section('contentTitle')
<h3> {{ $payment_gateway->payment_method }} Payments </h3>
@endsection

@section('content')

{{-- Download--}}
<ul class="nav justify-content-end pb-2">
    <li class="nav-item">
        <a class="nav-link bg-dark"
            href="{{ route('payment_gateways.customer_payments.create', ['payment_gateway' => $payment_gateway->id]) }}">
            <i class="fas fa-download"></i> Download Payments
        </a>
    </li>
</ul>
{{-- Download--}}

<div class="card">

    <!--modal -->
    <div class="modal fade" id="modal-customer">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="ModalBody">

                    <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>
                    <div class="text-bold pt-2">Loading...</div>
                    <div class="text-bold pt-2">Please Wait</div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /modal -->

    <div class="card-body">

        <table id="phpPaging" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Operator ID</th>
                    <th scope="col">Name <br> Username</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Status</th>
                    <th scope="col" style="width: 30%">Amount</th>
                    <th scope="col">Date</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($payments as $payment )
                <tr>
                    <td scope="row">{{ $payment->operator_id }}</td>
                    <td>
                        {{ $payment->name }} <br>
                        {{ $payment->username }}
                    </td>
                    <td>
                        <a href="#" onclick="showCustomerDetails('{{ $payment->customer_id }}')">
                            {{ $payment->mobile }}
                        </a>
                    </td>
                    <td>{{ $payment->pay_status }}</td>
                    <td>
                        <div class="row">
                            <div class="col-sm">
                                Amount Paid: {{ $payment->amount_paid }} <br>
                                Service Charge: {{ $payment->transaction_fee }} <br>
                                VAT: {{ $payment->vat_paid }}
                            </div>
                            <div class="col-sm">
                                Store Amount: {{ $payment->store_amount }} <br>
                                1st party: {{ $payment->first_party }} <br>
                                2nd party: {{ $payment->second_party }} <br>
                                3rd party: {{ $payment->third_party }} <br>
                            </div>
                        </div>
                    </td>
                    <td>{{ $payment->date }}</td>
                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    <!--/card-body-->

    <div class="card-footer">
        <div class="row">
            <div class="col-sm-2">
                Total Entries: {{ $payments->total() }}
            </div>
            <div class="col-sm-6">
                {{ $payments->withQueryString()->links() }}
            </div>
        </div>
    </div>
    <!--/card-footer-->

</div>

@endsection

@section('pageJs')

<script>
    function showCustomerDetails(customer)
    {
        $("#modal-title").html("Customer Details");
        $("#ModalBody").html('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>');
        $("#ModalBody").append('<div class="text-bold pt-2">Loading...</div>');
        $("#ModalBody").append('<div class="text-bold pt-2">Please Wait</div>');
        $('#modal-customer').modal('show');
        $.get( "/admin/customer-details/" + customer, function( data ) {
            $("#ModalBody").html(data);
        });
    }

</script>
@endsection