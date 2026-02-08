@section('contentTitle')
@endsection

@section('content')

<div class="row">

    <div class="col-md-6">

        <div class="card card-primary">

            <div class="card-header">
                <h3 class="card-title">Submit Payment</h3>
            </div>

            <form role="form" method="POST" action="{{ route('bulk_customer_bill_paids.store') }}">

                @csrf

                <div class="card-body">

                    {{-- no_of_invoice --}}
                    <div class="form-group">
                        <label for="no_of_invoice">
                            Number of Invoice
                        </label>
                        <input type="text" class="form-control" id="no_of_invoice" value="{{ $bill_count }}" disabled>
                    </div>
                    {{-- no_of_invoice --}}

                    {{-- amount_received --}}
                    <div class="form-group">
                        <label for="amount_received">
                            Amount Received From Customer
                        </label>
                        <input type="text" class="form-control" id="amount_received" value="{{ $customers_amount }}"
                            disabled>
                    </div>
                    {{-- amount_received --}}

                    {{-- amount_paid --}}
                    <div class="form-group">
                        <label for="amount_paid">
                            Amount Paid to Upstream
                        </label>
                        <input type="text" class="form-control" id="amount_paid" value="{{ $operator_amount }}"
                            disabled>
                    </div>
                    {{-- amount_paid --}}

                    {{-- balance --}}
                    <div class="form-group">
                        <label for="balance">
                            Remaining Account Balance
                        </label>
                        <input type="text" class="form-control" id="balance" value="{{ $balance }}" disabled>
                    </div>
                    {{-- balance --}}

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Confirm Payment</button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@section('pageJs')
@endsection