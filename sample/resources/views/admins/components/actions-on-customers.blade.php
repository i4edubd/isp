<div class="btn-group dropleft" role="group">

    <button id="btnGroupActionsOnCustomer" type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        Action
    </button>

    <div class="dropdown-menu" aria-labelledby="btnGroupActionsOnCustomer">

        @if (Auth::user()->subscription_status === 'suspended')

        <a class="dropdown-item" href="#">
            Subscription Suspended
        </a>

        @else

        {{-- --}}
        @if ($customer->payment_status == 'billed')
        <a class="dropdown-item" href="{{ route('customer_bills.index', ['customer_id' => $customer->id]) }}">
            Bills
        </a>
        @endif
        {{-- --}}
        @can('update', $customer)
        @if (isset($customers))
        <a class="dropdown-item"
            href="{{ route('customers.edit', ['customer' => $customer, 'page' => $customers->currentPage()]) }}">
            Edit
        </a>
        @else
        <a class="dropdown-item" href="{{ route('customers.edit', ['customer' => $customer, 'page' => 1]) }}">
            Edit
        </a>
        @endif
        @endcan
        {{-- --}}
        @can('delete', $customer)
        <a class="dropdown-item" href="#"
            onclick='deleteCustomer("{{ route("customers.destroy", ["customer" => $customer]) }}")'>
            Delete
        </a>
        @endcan
        {{-- --}}
         @can('activate', $customer)
        <a class="dropdown-item" id="{{ 'customer_action_Activate' . $customer->id }}"
            href="{{ '#row-' . $customer->id }}"
            onclick="callUsersActionURL('{{ route('customer-activate', ['customer' => $customer]) }}', '{{ 'customer_action_Activate' . $customer->id }}', '{{ $customer->id }}')">
            Activate
        </a>
        @elsecan('viewActivateOptions', $customer)
        <a class="dropdown-item" href="#"
            onclick="showActivateOptions('{{ route('customer-activate-options', ['id' => $customer]) }}')">
            Activate
        </a>
        @endcan
        {{-- --}} 
        @can('editSuspendDate', $customer)
        <a class="dropdown-item" href="{{ route('customers.suspend_date.create', ['customer' => $customer]) }}">
            Edit Suspend Date <i class="fas fa-user-shield"></i>
        </a>
        @endcan
        {{-- --}}
        @can('suspend', $customer)
        <a class="dropdown-item" id="{{ 'customer_action_Suspend' . $customer->id }}"
            href="{{ '#row-' . $customer->id }}"
            onclick="callUsersActionURL('{{ route('customer-suspend', ['customer' => $customer]) }}', '{{ 'customer_action_Suspend' . $customer->id }}', '{{ $customer->id }}')">
            Suspend
        </a>
        @endcan
        {{-- --}}
        @can('disable', $customer)
        <a class="dropdown-item" id="{{ 'customer_action_Disable' . $customer->id }}"
            href="{{ '#row-' . $customer->id }}"
            onclick="callUsersActionURL('{{ route('customer-disable', ['customer' => $customer]) }}', '{{ 'customer_action_Disable' . $customer->id }}', '{{ $customer->id }}')">
            Disable
        </a>
        @endcan
        {{-- --}}
        @can('editSpeedLimit', $customer)
        <a class="dropdown-item" href="{{ route('customer-package-time-limit.edit', ['customer' => $customer]) }}">
            Edit Time <i class="fas fa-user-shield"></i>
        </a>
        <a class="dropdown-item" href="{{ route('customer-package-speed-limit.edit', ['customer' => $customer]) }}">
            Edit Speed <i class="fas fa-user-shield"></i>
        </a>
        <a class="dropdown-item" href="{{ route('customer-package-volume-limit.edit', ['customer' => $customer]) }}">
            Edit Volume <i class="fas fa-user-shield"></i>
        </a>
        @endcan
        {{-- --}}
        @can('changePackage', $customer)
        <a class="dropdown-item" href="{{ route('customer-package-change.edit', ['customer' => $customer]) }}">
            Change Package
        </a>
        @endcan
        {{-- --}}
        @can('dailyRecharge', $customer)
        <a class="dropdown-item" href="{{ route('ppp-daily-recharge.edit', ['customer' => $customer]) }}">
            Recharge <i class="text-warning fas fa-level-up-alt"></i>
        </a>
        <a class="dropdown-item" href="{{ route('daily-billing-package-change.edit', ['customer' => $customer]) }}">
            Change Package
        </a>
        @endcan
        {{-- --}}
        @can('hotspotRecharge', $customer)
        <a class="dropdown-item" href="{{ route('hotspot-recharge.edit', ['customer' => $customer]) }}">
            Recharge <i class="text-warning fas fa-level-up-alt"></i>
        </a>
        <a class="dropdown-item" href="{{ route('hotspot-package-change.edit', ['customer' => $customer]) }}">
            Change Package
        </a>
        @endcan
        {{-- --}}
        @can('changeOperator', $customer)
        <a class="dropdown-item" href="{{ route('customers.change_operator.create', ['customer' => $customer]) }}">
            Change Operator
        </a>
        @endcan
        {{-- --}}
        @can('generateBill', $customer)
        <a class="dropdown-item" href="{{ route('customers.customer_bills.create', ['customer' => $customer]) }}">
            Generate Bill
        </a>
        @endcan
        {{-- --}}
        @can('editBillingProfile', $customer)
        <a class="dropdown-item" href="{{ route('customer-billing-profile-edit.edit', ['customer' => $customer]) }}">
            Edit Billing Profile
        </a>
        @endcan
        {{-- --}}
        @can('removeMacBind', $customer)
        <a class="dropdown-item" id="{{ 'customer_action_MAC' . $customer->id }}" href="{{ '#row-' . $customer->id }}"
            onclick="callUsersActionURL('{{ route('mac-bind-destroy', ['customer' => $customer]) }}', '{{ 'customer_action_MAC' . $customer->id }}', '{{ $customer->id }}')">
            Remove MAC Bind
        </a>
        @endcan
        {{-- --}}
        @can('sendSms', $customer)
        <a class="dropdown-item" href="{{ route('customers.sms_histories.create', ['customer' => $customer]) }}">
            Send SMS
        </a>
        @endcan
        {{-- --}}
        @can('sendLink', $customer)
        <a class="dropdown-item" href="{{ route('customer.send-payment-link.create', ['customer' => $customer]) }}">
            Send Payment Link
        </a>
        @endcan
        {{-- --}}
        @can('advancePayment', $customer)
        <a class="dropdown-item" href="{{ route('customers.advance_payment.create', ['customer' => $customer]) }}">
            Advance Payment
        </a>
        @endcan
        {{-- --}}
        @can('activateFup', $customer)
        <a class="dropdown-item" id="{{ 'customer_action_FUP' . $customer->id }}" href="{{ '#row-' . $customer->id }}"
            onclick="callUsersActionURL('{{ route('activate-fup', ['customer' => $customer]) }}', '{{ 'customer_action_FUP' . $customer->id }}', '{{ $customer->id }}')">
            activate FUP
        </a>
        @endcan
        {{-- --}}
        <a class="dropdown-item" href="{{ route('customers.customer_complains.create', ['customer' => $customer]) }}">
            Complaint
        </a>
        {{-- --}}
        @can('downloadInternetHistory', $customer)
        <a class="dropdown-item" href="{{ route('customers.internet-history.create', ['customer' => $customer]) }}">
            Internet History <i class="fas fa-download"></i>
        </a>
        @endcan
        {{-- --}}
        @can('customPrice', $customer)
        <a class="dropdown-item" href="#"
            onclick="showSpecialPrice('{{ route('customers.custom_prices.index', ['customer' => $customer]) }}')">
            Special Price
        </a>
        @endcan
        {{-- --}}
        @can('addChild', $customer)
        <a class="dropdown-item" href="{{ route('temp_customers.create', ['parent_id' => $customer->id]) }}">
            Add Child Account
        </a>
        @endcan
        {{-- --}}
        @can('makeChild', $customer)
        <a class="dropdown-item" href="{{ route('customers.make_child.create', ['customer' => $customer]) }}">
            Make Child
        </a>
        @endcan
        {{-- --}}
        @can('makeParent', $customer)
        <a class="dropdown-item" id="{{ 'customer_action_makeParent' . $customer->id }}"
            href="{{ '#row-' . $customer->id }}"
            onclick="callUsersActionURL('{{ route('customers.make_parent', ['child' => $customer]) }}', '{{ 'customer_action_makeParent' . $customer->id }}', '{{ $customer->id }}')">
            Make Parent
        </a>
        @endcan
        {{-- --}}
        <a class="dropdown-item" href="{{ route('customers.others-payments.create', ['customer' => $customer]) }}">
            Other Payment
        </a>
        {{-- --}}
        @can('disconnect', $customer)
        <a class="dropdown-item" id="{{ 'customer_action_Disconnect' . $customer->id }}"
            href="{{ '#row-' . $customer->id }}"
            onclick="callUsersActionURL('{{ route('customers.disconnect.create', ['customer' => $customer]) }}', '{{ 'customer_action_Disconnect' . $customer->id }}', '{{ $customer->id }}')">
            Disconnect
        </a>
        @endcan
        {{-- --}}
        @can('editIP', $customer)
        <a class="dropdown-item" id="{{ 'editIP' . $customer->id }}" href="{{ '#row-' . $customer->id }}"
            onclick="editIP('{{ route('customers.edit-ip.create', ['customer' => $customer]) }}')">
            Edit IP
        </a>
        @endcan
        {{-- --}}
        @endif

    </div>

</div>
