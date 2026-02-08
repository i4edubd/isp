<div class="btn-group" role="group">

    <button id="btnGroupActionsOnCustomer" type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        Action
    </button>

    <div class="dropdown-menu" aria-labelledby="btnGroupActionsOnCustomer">

        {{--  --}}
        @can('receivePayment', $bill)
        <a class="dropdown-item"
            href="{{ route('customer_bills.cash-payments.create', ['customer_bill' => $bill->id]) }}">
            Paid
        </a>
        @endcan
        {{--  --}}
        @can('editInvoice', $bill)
        <a class="dropdown-item" href="{{ route('customer_bills.edit', ['customer_bill' => $bill->id]) }}">
            Edit
        </a>
        @endcan
        {{--  --}}
        @can('deleteInvoice', $bill)
        <a class="dropdown-item" href="#"
            onclick='deleteBill("{{ route("customer_bills.destroy", ["customer_bill" => $bill->id]) }}")'>
            Delete
        </a>
        @endcan
        {{--  --}}
        @can('printInvoice', $bill)
        <a class="dropdown-item" href="{{ route('customer_bills.print', ['customer_bill' => $bill->id]) }}">
            Print/Download
        </a>
        @endcan
        {{--  --}}

    </div>

</div>
