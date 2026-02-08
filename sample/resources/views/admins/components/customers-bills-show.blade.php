<table id="phpPaging" class="table table-bordered">

    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Customer ID</th>
            <th scope="col">Username</th>
            <th scope="col">Mobile</th>
            <th scope="col">package</th>
            <th scope="col">Amount</th>
            <th scope="col">Billing Period</th>
            <th scope="col">Due Date</th>
            <th scope="col">Purpose</th>
            <th scope="col"></th>
        </tr>
    </thead>

    <tbody>

        @foreach ($bills as $bill )

        <tr>
            <td>{{ $bill->id }}</td>
            <th>{{ $bill->customer_id }}</th>
            <th>{{ $bill->username }}</th>
            <td>
                <a href="#" onclick="showCustomerDetails('{{ $bill->customer_id }}')">
                    {{ $bill->mobile }}
                </a>
            </td>
            <td>{{ $bill->description }}</td>
            <td>{{ $bill->amount }}</td>
            <td>{{ $bill->billing_period }}</td>
            <td>{{ $bill->due_date }}</td>
            <td>{{ $bill->purpose }}</td>
            <td>
                @include('admins.components.actions-on-customers-bills')
            </td>
        </tr>

        @endforeach

    </tbody>

</table>
