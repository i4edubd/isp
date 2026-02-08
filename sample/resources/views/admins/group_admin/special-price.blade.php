@if ($custom_price->id == 0)

<h1 class="display-4">No special price found for this customer.</h1>

<a class="btn btn-dark" href="{{ route('customers.custom_prices.create', ['customer' => $customer->id]) }}"
    role="button">
    CREATE
</a>

@else

<p class="lead">
    <ul class="list-group">
        <li class="list-group-item">Customer ID: {{ $customer->id }}</li>
        <li class="list-group-item">Customer Username: {{ $customer->username }}</li>
        <li class="list-group-item">Package Name: {{ $custom_price->package->name }}</li>
        <li class="list-group-item">
            Regular Price: {{ $custom_price->package->price }} {{ config('consumer.currency') }}
        </li>
        <li class="list-group-item">
            Special Price: {{ $custom_price->price }} {{ config('consumer.currency') }}
        </li>
    </ul>
</p>

<form method="POST"
    action="{{ route('customers.custom_prices.destroy', ['customer' => $customer->id, 'custom_price' => $custom_price->id]) }}">
    @csrf
    @method('DELETE')

    <ul class="nav flex-column flex-sm-row">

        <li class="nav-item">
            <a class="btn btn-primary"
                href="{{ route('customers.custom_prices.edit', ['customer' => $customer->id, 'custom_price' => $custom_price->id]) }}"
                role="button">
                EDIT
            </a>
        </li>

        <li class="nav-item ml-4">
            <button type="submit" class="btn btn-danger">DELETE</button>
        </li>

    </ul>

</form>
@endif
