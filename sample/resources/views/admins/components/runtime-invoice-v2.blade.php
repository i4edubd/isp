@if (isset($invoice))
    <ul class="list-group list-group-flush">

        <li class="list-group-item list-group-item-secondary">
            Payable Amounts <i class="fas fa-arrow-circle-down ml-2"></i>
        </li>

        <li class="list-group-item">
            <span class="font-weight-bold"> Customer's Amount: </span>
            @if ($invoice->get('customers_discount_amount') > 0)
                {{ $invoice->get('customers_bill_amount') }} (bill) -
                {{ $invoice->get('customers_discount_amount') }} (discount) =
            @endif
            {{ $invoice->get('customers_payable_amount') }}
            {{ $invoice->get('currency') }}
        </li>

        <li class="list-group-item">
            <span class="font-weight-bold"> Operator's Amount: </span>
            @if ($invoice->get('operators_discount_amount') > 0)
                {{ $invoice->get('operators_bill_amount') }} (bill) -
                {{ $invoice->get('operators_discount_amount') }} (discount) =
            @endif
            {{ $invoice->get('operators_payable_amount') }}
            {{ $invoice->get('currency') }}
        </li>

        <li class="list-group-item list-group-item-secondary">
            Description/Calculations of the bill<i class="fas fa-arrow-circle-down ml-2"></i>
        </li>

        <li class="list-group-item">
            <span class="font-weight-bold"> package Name: </span>
            {{ $invoice->get('package_name') }}
        </li>

        <li class="list-group-item">
            <span class="font-weight-bold"> Package Price: </span>
            {{ $invoice->get('package_customers_price') }} {{ $invoice->get('currency') }} (For Customer),
            {{ $invoice->get('package_operators_price') }} {{ $invoice->get('currency') }} (For Operator)
        </li>

        @if ($invoice->get('customers_unit_price'))
            <li class="list-group-item">
                <span class="font-weight-bold"> Unit Price: </span>
                {{ $invoice->get('customers_unit_price') }} {{ $invoice->get('currency') }} /
                {{ $invoice->get('interval_unit') }} (For Customer),
                {{ $invoice->get('operators_unit_price') }} {{ $invoice->get('currency') }} /
                {{ $invoice->get('interval_unit') }} (For Operator),
            </li>
        @endif

        @if ($invoice->get('bill_period'))
            <li class="list-group-item">
                <span class="font-weight-bold"> Bill Period: </span>
                {{ $invoice->get('bill_period') }}
                @if ($invoice->get('interval_count'))
                    ( {{ $invoice->get('interval_count') }} {{ $invoice->get('interval_unit') }} )
                @endif
                @if ($invoice->get('interval_unit') == 'Minute')
                    / ({{ mToDhm($invoice->get('interval_count')) }})
                @endif
            </li>
        @endif

        @if ($invoice->get('customers_bill_amount'))
            <li class="list-group-item">
                <span class="font-weight-bold"> Customer's Bill Amount: </span>
                {{ $invoice->get('interval_count') }} {{ $invoice->get('interval_unit') }} x
                {{ $invoice->get('customers_unit_price') }} = {{ $invoice->get('customers_bill_amount') }}
                {{ $invoice->get('currency') }}
            </li>
        @endif

        @if ($invoice->get('operators_bill_amount'))
            <li class="list-group-item">
                <span class="font-weight-bold"> Operator's Bill Amount: </span>
                {{ $invoice->get('interval_count') }} {{ $invoice->get('interval_unit') }} x
                {{ $invoice->get('operators_unit_price') }} = {{ $invoice->get('operators_bill_amount') }}
                {{ $invoice->get('currency') }}
            </li>
        @endif

        @if ($invoice->get('customers_discount_amount') > 0)
            <li class="list-group-item list-group-item-secondary">
                Description/Calculations of the Discount <i class="fas fa-arrow-circle-down ml-2"></i>
            </li>

            <li class="list-group-item">
                <span class="font-weight-bold"> package Name: </span>
                {{ $invoice->get('discount_package_name') }}
            </li>

            <li class="list-group-item">
                <span class="font-weight-bold"> Package Price: </span>
                {{ $invoice->get('discount_package_customers_price') }} {{ $invoice->get('currency') }} (For
                Customer),
                {{ $invoice->get('discount_package_operators_price') }} {{ $invoice->get('currency') }} (For Operator)
            </li>

            @if ($invoice->get('discount_customers_unit_price'))
                <li class="list-group-item">
                    <span class="font-weight-bold"> Unit Price: </span>
                    {{ $invoice->get('discount_customers_unit_price') }} {{ $invoice->get('currency') }} /
                    {{ $invoice->get('interval_unit') }} (For Customer),
                    {{ $invoice->get('discount_operators_unit_price') }} {{ $invoice->get('currency') }} /
                    {{ $invoice->get('interval_unit') }} (For Operator)
                </li>
            @endif

            @if ($invoice->get('discount_bill_period'))
                <li class="list-group-item">
                    <span class="font-weight-bold"> Bill Period: </span>
                    {{ $invoice->get('discount_bill_period') }}
                    @if ($invoice->get('discount_interval_count'))
                        ( {{ $invoice->get('discount_interval_count') }} {{ $invoice->get('interval_unit') }} )
                    @endif
                    @if ($invoice->get('interval_unit') == 'Minute')
                        / ({{ mToDhm($invoice->get('discount_interval_count')) }})
                    @endif
                </li>
            @endif

            @if ($invoice->get('customers_discount_amount'))
                <li class="list-group-item">
                    <span class="font-weight-bold"> Customer's Discount Amount: </span>
                    {{ $invoice->get('discount_interval_count') }} {{ $invoice->get('interval_unit') }} x
                    {{ $invoice->get('discount_customers_unit_price') }} =
                    {{ $invoice->get('customers_discount_amount') }}
                    {{ $invoice->get('currency') }}
                </li>
            @endif

            @if ($invoice->get('operators_discount_amount'))
                <li class="list-group-item">
                    <span class="font-weight-bold"> Operator's Discount Amount: </span>
                    {{ $invoice->get('discount_interval_count') }} {{ $invoice->get('interval_unit') }} x
                    {{ $invoice->get('discount_operators_unit_price') }} =
                    {{ $invoice->get('operators_discount_amount') }}
                    {{ $invoice->get('currency') }}
                </li>
            @endif
        @endif

        @if ($invoice->get('next_payment_date'))
            <li class="list-group-item list-group-item-secondary">
                <span class="font-weight-bold"> Next Payment Date </span>
                <i class="fas fa-arrow-right"></i>
                {{ $invoice->get('next_payment_date') }}
            </li>
        @endif

    </ul>
@endif
