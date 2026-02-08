<td style="text-align: center;">
    <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}">
</td>

<td scope="row">
    {{ $customer->id }}
    <br>
    @if ($customer->is_online)
    <i class="fas fa-circle text-success"></i>
    @else
    <i class="fas fa-circle text-danger"></i>
    @endif
</td>

<td>
    <a href="#" onclick="showCustomerDetails('{{ $customer->id }}')">
        {{ $customer->mobile }}
    </a>
    <br>
    {{ $customer->name }}
</td>

<td>
    {{ $customer->username }}
    <br>
    {{ $customer->password }}
</td>
<td>
    {{ $customer->package_name }}
    <br>
    {{ $customer->package_expired_at }}
    <br>
    {{ $customer->remaining_validity }}
</td>
<td>
    <span class="{{ $customer->payment_color }}"> {{ $customer->payment_status }} </span>
    <br>
    <span class="{{ $customer->color }}"> {{ $customer->status }} </span>

</td>
<td>
    @include('admins.components.actions-on-customers')
</td>