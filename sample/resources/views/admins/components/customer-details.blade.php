<div class="container-fluid">
    {{-- Navigation bar --}}
    <ul class="nav nav-tabs" id="myTab" role="tablist">

        <li class="nav-item">
            <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                aria-controls="profile" aria-selected="true">Profile</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" id="Bills-tab" data-toggle="tab" href="#Bills" role="tab" aria-controls="Bills"
                aria-selected="false">Bills</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" id="PaymentHistory-tab" data-toggle="tab" href="#PaymentHistory" role="tab"
                aria-controls="PaymentHistory" aria-selected="false">Payment History</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" id="InternetHistory-tab" data-toggle="tab" href="#InternetHistory" role="tab"
                aria-controls="InternetHistory" aria-selected="false">Internet History</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" id="Bandwidth-graph-tab" data-toggle="tab" href="#BandwidthGraph" role="tab"
                aria-controls="BandwidthGraph" aria-selected="false">
                Bandwidth Graph
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" id="SmsHistory-tab" data-toggle="tab" href="#SmsHistory" role="tab"
                aria-controls="SmsHistory" aria-selected="false">SMS History</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" id="customer_change_logs-tab" data-toggle="tab" href="#customer_change_logs"
                role="tab" aria-controls="customer_change_logs" aria-selected="false">Change Logs</a>
        </li>

    </ul>
    {{-- Navigation bar --}}

    <div class="tab-content" id="myTabContent">

        {{-- Profile --}}
<div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">

    <div class="row">

        {{-- First Column --}}
        <div class="col-sm-4">

            {{-- General Information --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    General Information
                </div>
                <div class="card-body">

                    {{-- online status --}}
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @if ($is_online)
                                <i class="fas fa-circle text-success"></i> Online
                            @else
                                <i class="fas fa-circle text-danger"></i> Offline
                            @endif
                        </li>
                    </ul>
                    {{-- online status --}}

                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Operator ID
                            <span class="badge badge-primary badge-pill">{{ $customer->operator_id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Company
                            <span class="badge badge-secondary badge-pill">{{ $customer->company }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Type
                            <span class="badge badge-info badge-pill">{{ $customer->connection_type }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Status
                            <span class="badge badge-danger badge-pill">{{ $customer->status }}</span>
                        </li>
                        @if ($customer->status === 'suspended')
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Suspend Reason
                                <span class="badge badge-danger badge-pill">{{ $customer->suspend_reason }}</span>
                            </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Profile
                            <span class="badge badge-warning badge-pill">
                                {{ $customer->billing_profile }}({{ $customer->billing_type }})
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Name
                            <span class="badge badge-success badge-pill">{{ $customer->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Mobile
                            <span class="badge badge-primary badge-pill">{{ $customer->mobile }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Email
                            <span class="badge badge-secondary badge-pill">{{ $customer->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Payment Status
                            <span class="badge badge-success badge-pill">{{ $customer->payment_status }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Advance Payment
                            <span class="badge badge-info badge-pill">{{ $customer->advance_payment }}
                                {{ config('consumer.currency') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Zone
                            <span class="badge badge-warning badge-pill">{{ $customer->zone }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            NID Number
                            <span class="badge badge-primary badge-pill">{{ $customer->nid }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Registration Date
                            <span class="badge badge-secondary badge-pill">{{ $customer->registration_date }}</span>
                        </li>
                        @foreach ($customer->custom_attributes as $custom_attribute)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $custom_attribute->name }}
                                <span class="badge badge-info badge-pill">{{ $custom_attribute->value }}</span>
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>
            {{-- General Information --}}

        </div>
        {{-- First Column --}}

        {{-- Second Column --}}
        <div class="col-sm-4">

            {{-- Username & Password --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Username & Password
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Username
                            <span class="badge badge-pill badge-warning">{{ $customer->username }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Password
                            <span class="badge badge-secondary badge-pill">{{ $customer->password }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            {{-- Username & Password --}}

            {{-- Package Information --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Package Information
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Package
                            <span class="badge badge-info badge-pill">{{ $customer->package_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Last Update
                            <span class="badge badge-warning badge-pill">{{ $customer->last_recharge_time }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Valid Until
                            <span class="badge badge-danger badge-pill">{{ $customer->package_expired_at }}</span>
                        </li>
                        @if ($customer->rate_limit)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Rate Limit
                                <span class="badge badge-success badge-pill">{{ $customer->rate_limit }}</span>
                            </li>
                        @else
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Rate Limit
                                <span class="badge badge-secondary badge-pill">N/A</span>
                            </li>
                        @endif
                        @if ($customer->total_octet_limit)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Volume Limit
                                <span class="badge badge-info badge-pill">{{ $customer->total_octet_limit / 1000 / 1000 / 1000 }} GB</span>
                            </li>
                        @else
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Volume Limit
                                <span class="badge badge-secondary badge-pill">N/A</span>
                            </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Volume Used
                            <span class="badge badge-warning badge-pill">
                                {{ ($customer->radaccts->sum('acctoutputoctets') +
                                    $customer->radaccts->sum('acctinputoctets') +
                                    $radaccts_history->sum('acctoutputoctets') +
                                    $radaccts_history->sum('acctinputoctets')) /
                                    1000 /
                                    1000 /
                                    1000 }}
                                GB
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            {{-- Package Information --}}

            {{-- Login to the customer's device --}}
            @if (is_null($customer->login_ip) == false)
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        Login to the customer's device
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ 'http://' . $customer->login_ip }}" target="_blank">
                                    {{ 'http://' . $customer->login_ip }}
                                </a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ 'http://' . $customer->login_ip . ':8088' }}" target="_blank">
                                    {{ 'http://' . $customer->login_ip . ':8088' }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
            {{-- Login to the customer's device --}}

        </div>
        {{-- Second Column --}}

        {{-- Third Column --}}
        <div class="col-sm-4">
            
            {{-- Customer Address --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Customer Address
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {!! $customer->address !!}
                        </li>
                    </ul>
                </div>
            </div>
            {{-- Customer Address --}}

            {{-- Router & IP Address --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Router & IP Address
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Router
                            <span class="badge badge-primary badge-pill">
                                {{ $customer->router->id > 0 ? $customer->router->nasname : $customer->router_ip }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            IP Address
                            <span class="badge badge-pill badge-warning">
                                <a href="http://{{ $customer->login_ip }}" target="_blank">{{ $customer->login_ip }}</a>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            {{-- Router & IP Address --}}

            {{-- MAC Address & MAC Bind --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    MAC Address & MAC Bind
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            MAC Address
                            <span class="badge badge-info badge-pill">{{ $customer->login_mac_address }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            MAC Bind
                            <span class="badge badge-warning badge-pill">{{ $customer->mac_bind }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            {{-- MAC Address & MAC Bind --}}

            {{-- Comment --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Comment
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {!! $customer->comment !!}
                        </li>
                    </ul>
                </div>
            </div>
            {{-- Comment --}}

        </div>
        {{-- Third Column --}}

    </div>
    {{-- row --}}

</div>
{{-- Profile --}}
        
        {{-- Bills --}}
        <div class="tab-pane fade" id="Bills" role="tabpanel" aria-labelledby="Bills-tab">
            <div class="card">
                <div class="card-header bg-success text-white">
                    Bills
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Customer ID</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Mobile</th>
                                    <th scope="col">Package</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Billing Period</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Purpose</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bills as $bill)
                                    <tr>
                                        <td>{{ $bill->id }}</td>
                                        <td>{{ $bill->customer_id }}</td>
                                        <td>{{ $bill->username }}</td>
                                        <td>{{ $bill->mobile }}</td>
                                        <td>{{ $bill->description }}</td>
                                        <td>{{ $bill->amount }}</td>
                                        <td>{{ $bill->billing_period }}</td>
                                        <td>{{ $bill->due_date }}</td>
                                        <td>{{ $bill->purpose }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button id="btnGroupActionsOnCustomer" type="button"
                                                    class="btn btn-danger dropdown-toggle" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="btnGroupActionsOnCustomer">
                                                    @can('receivePayment', $bill)
                                                        <a class="dropdown-item"
                                                            href="{{ route('customer_bills.cash-payments.create', ['customer_bill' => $bill->id]) }}">
                                                            Paid
                                                        </a>
                                                    @endcan
                                                    @can('editInvoice', $bill)
                                                        <a class="dropdown-item"
                                                            href="{{ route('customer_bills.edit', ['customer_bill' => $bill->id]) }}">
                                                            Edit
                                                        </a>
                                                    @endcan
                                                    @can('printInvoice', $bill)
                                                        <a class="dropdown-item"
                                                            href="{{ route('customer_bills.print', ['customer_bill' => $bill->id]) }}">
                                                            Print/Download
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Bills --}}

        {{-- Payment History --}}
        <div class="tab-pane fade" id="PaymentHistory" role="tabpanel" aria-labelledby="PaymentHistory-tab">
            <div class="card">
                <div class="card-header bg-success text-white">
                    Payment History
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Payment Gateway</th>
                                    <th scope="col">Pay Status</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Validity</th>
                                    <th scope="col">Transaction Fee</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">TxnID/PIN</th>
                                    <th scope="col">Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_gateway_name }}</td>
                                        <td>{{ $payment->pay_status }}</td>
                                        <td>{{ $payment->amount_paid }}</td>
                                        <td>{{ $payment->validity_period }}</td>
                                        <td>{{ $payment->transaction_fee }}</td>
                                        <td>{{ $payment->created_at }}</td>
                                        <td>{{ $payment->bank_txnid }}</td>
                                        <td>{{ $payment->purpose }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Payment History --}}

        {{-- Internet History --}}
        <div class="tab-pane fade" id="InternetHistory" role="tabpanel" aria-labelledby="InternetHistory-tab">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Internet History
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Start Time</th>
                                    <th scope="col">Stop Time</th>
                                    <th scope="col">Total Time</th>
                                    <th scope="col">Terminate Cause</th>
                                    <th scope="col">Download(MB)</th>
                                    <th scope="col">Upload(MB)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_download = 0;
                                    $total_upload = 0;
                                @endphp
                                @foreach ($customer->radaccts->sortBy('acctstoptime') as $radacct)
                                    @php
                                        $total_download += $radacct->acctoutputoctets;
                                        $total_upload += $radacct->acctinputoctets;
                                    @endphp
                                    <tr>
                                        <td>{{ $radacct->acctstarttime }}</td>
                                        <td>{{ $radacct->acctstoptime }}</td>
                                        <td>{{ sToHms($radacct->acctsessiontime) }}</td>
                                        <td>{{ $radacct->acctterminatecause }}</td>
                                        <td>{{ $radacct->acctoutputoctets / 1000000 }}</td>
                                        <td>{{ $radacct->acctinputoctets / 1000000 }}</td>
                                    </tr>
                                @endforeach
                                @foreach ($radaccts_history->sortByDesc('acctstoptime') as $radacct_history)
                                    @php
                                        $total_download += $radacct_history->acctoutputoctets;
                                        $total_upload += $radacct_history->acctinputoctets;
                                    @endphp
                                    <tr>
                                        <td>{{ $radacct_history->acctstarttime }}</td>
                                        <td>{{ $radacct_history->acctstoptime }}</td>
                                        <td>{{ sToHms($radacct_history->acctsessiontime) }}</td>
                                        <td>{{ $radacct_history->acctterminatecause }}</td>
                                        <td>{{ $radacct_history->acctoutputoctets / 1000000 }}</td>
                                        <td>{{ $radacct_history->acctinputoctets / 1000000 }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Total:</td>
                                    <td>{{ $total_download / 1000000 }}</td>
                                    <td>{{ $total_upload / 1000000 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Internet History --}}

        {{-- bandwidth-graph --}}
        <div class="tab-pane fade" id="BandwidthGraph" role="tabpanel" aria-labelledby="Bandwidth-graph-tab">
            <div class="card-body">
                <h5 class="font-weight-bold text-primary">Hourly Graph</h5>
                <div class="mb-4">
                    <img class="img-fluid rounded shadow" src="{{ $graph->get('hourly') }}" alt="Image Not Found">
                </div>
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold text-primary">Daily Graph</h5>
                <div class="mb-4">
                    <img class="img-fluid rounded shadow" src="{{ $graph->get('daily') }}" alt="Image Not Found">
                </div>
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold text-primary">Weekly Graph</h5>
                <div class="mb-4">
                    <img class="img-fluid rounded shadow" src="{{ $graph->get('weekly') }}" alt="Image Not Found">
                </div>
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold text-primary">Monthly Graph</h5>
                <div class="mb-4">
                    <img class="img-fluid rounded shadow" src="{{ $graph->get('monthly') }}" alt="Image Not Found">
                </div>
            </div>
        </div>
        {{-- bandwidth-graph --}}

        {{-- SMS History --}}
        <div class="tab-pane fade" id="SmsHistory" role="tabpanel" aria-labelledby="SmsHistory-tab">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">To Number</th>
                            <th scope="col">Status</th>
                            <th scope="col">SMS Count</th>
                            <th scope="col">SMS Cost</th>
                            <th scope="col">SMS Body</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customer->sms_histories as $sms_history)
                            <tr>
                                <td>{{ $sms_history->to_number }}</td>
                                <td><span class="badge badge-info">{{ $sms_history->status_text }}</span></td>
                                <td>{{ $sms_history->sms_count }}</td>
                                <td>{{ $sms_history->sms_cost }}</td>
                                <td>{{ $sms_history->sms_body }}</td>
                                <td>{{ $sms_history->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- SMS History --}}

        {{-- customer_change_logs --}}
        <div class="tab-pane fade" id="customer_change_logs" role="tabpanel" aria-labelledby="customer_change_logs-tab">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Topic</th>
                            <th scope="col">Changed By</th>
                            <th scope="col">Change Log</th>
                            <th scope="col">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customer->customer_change_logs->sortByDesc('created_at') as $customer_change_log)
                            <tr>
                                <td>{{ $customer_change_log->topic }}</td>
                                <td>{{ $customer_change_log->changed_by }}</td>
                                <td>{{ $customer_change_log->change_log }}</td>
                                <td>{{ $customer_change_log->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- customer_change_logs --}}

    </div>

    {{-- Actions --}}
    <fieldset class="border border-info p-3 rounded shadow-sm bg-light">

        <legend class="w-auto text-info font-weight-bold">Actions</legend>

        <div class="d-flex flex-wrap justify-content-start">

            @if (Auth::user()->subscription_status === 'suspended')
                <a class="btn btn-outline-danger m-2" href="#">
                    Subscription Suspended
                </a>
            @else
                @if ($customer->payment_status == 'billed')
                    <a class="btn btn-outline-primary m-2"
                        href="{{ route('customer_bills.index', ['customer_id' => $customer->id]) }}">
                        Bills
                    </a>
                @endif
                @can('update', $customer)
                    @if (isset($customers))
                        <a class="btn btn-outline-secondary m-2"
                            href="{{ route('customers.edit', ['customer' => $customer, 'page' => $customers->currentPage()]) }}">
                            Edit
                        </a>
                    @else
                        <a class="btn btn-outline-secondary m-2"
                            href="{{ route('customers.edit', ['customer' => $customer, 'page' => 1]) }}">
                            Edit
                        </a>
                    @endif
                @endcan                
		@can('activate', $customer)
                    <a class="btn btn-outline-success m-2" id="{{ 'customer_details_action_Activate' . $customer->id }}" href="#"
                        onclick="callUsersActionURL('{{ route('customer-activate', ['customer' => $customer]) }}', '{{ 'customer_details_action_Activate' . $customer->id }}')">
                        Activate
                    </a>
                @endcan
                @can('editSuspendDate', $customer)
                    <a class="btn btn-outline-dark m-2"
                        href="{{ route('customers.suspend_date.create', ['customer' => $customer->id]) }}">
                        Edit Suspend Date <i class="fas fa-user-shield"></i>
                    </a>
                @endcan
                @can('suspend', $customer)
                    <a class="btn btn-outline-danger m-2" id="{{ 'customer_details_action_Suspend' . $customer->id }}" href="#"
                        onclick="callUsersActionURL('{{ route('customer-suspend', ['customer' => $customer]) }}', '{{ 'customer_details_action_Suspend' . $customer->id }}')">
                        Suspend
                    </a>
                @endcan
                
                @can('editSpeedLimit', $customer)
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('customer-package-time-limit.edit', ['customer' => $customer]) }}">
                        Edit Time <i class="fas fa-user-shield"></i>
                    </a>
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('customer-package-speed-limit.edit', ['customer' => $customer]) }}">
                        Edit Speed <i class="fas fa-user-shield"></i>
                    </a>
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('customer-package-volume-limit.edit', ['customer' => $customer]) }}">
                        Edit Volume <i class="fas fa-user-shield"></i>
                    </a>
                @endcan
                @can('changePackage', $customer)
                    <a class="btn btn-outline-primary m-2"
                        href="{{ route('customer-package-change.edit', ['customer' => $customer]) }}">
                        Change Package
                    </a>
                @endcan
                @can('dailyRecharge', $customer)
                    <a class="btn btn-outline-info m-2" href="{{ route('ppp-daily-recharge.edit', ['customer' => $customer]) }}">
                        Recharge <i class="text-warning fas fa-level-up-alt"></i>
                    </a>
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('daily-billing-package-change.edit', ['customer' => $customer]) }}">
                        Change Package
                    </a>
                @endcan
                @can('hotspotRecharge', $customer)
                    <a class="btn btn-outline-info m-2" href="{{ route('hotspot-recharge.edit', ['customer' => $customer->id]) }}">
                        Recharge <i class="text-warning fas fa-level-up-alt"></i>
                    </a>
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('hotspot-package-change.edit', ['customer' => $customer->id]) }}">
                        Change Package
                    </a>
                @endcan
                @can('changeOperator', $customer)
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('customers.change_operator.create', ['customer' => $customer->id]) }}">
                        Change Operator
                    </a>
                @endcan
                @can('generateBill', $customer)
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('customers.customer_bills.create', ['customer' => $customer->id]) }}">
                        Generate Bill
                    </a>
                @endcan
                @can('editBillingProfile', $customer)
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('customer-billing-profile-edit.edit', ['customer' => $customer->id]) }}">
                        Edit Billing Profile
                    </a>
                @endcan
                @can('removeMacBind', $customer)
                    <a class="btn btn-outline-danger m-2" id="{{ 'customer_details_action_MAC' . $customer->id }}" href="#"
                        onclick="callUsersActionURL('{{ route('mac-bind-destroy', ['customer' => $customer]) }}', '{{ 'customer_details_action_MAC' . $customer->id }}')">
                        Remove MAC Bind
                    </a>
                @endcan
                @can('sendSms', $customer)
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('customers.sms_histories.create', ['customer' => $customer->id]) }}">
                        Send SMS
                    </a>
                @endcan
                @can('sendLink', $customer)
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('customer.send-payment-link.create', ['customer' => $customer->id]) }}">
                        Send Payment Link
                    </a>
                @endcan
                @can('advancePayment', $customer)
                    <a class="btn btn-outline-info m-2"
                        href="{{ route('customers.advance_payment.create', ['customer' => $customer->id]) }}">
                        Advance Payment
                    </a>
                @endcan
                @can('activateFup', $customer)
                    <a class="btn btn-outline-info m-2" id="{{ 'customer_details_action_FUP' . $customer->id }}" href="#"
                        onclick="callUsersActionURL('{{ route('activate-fup', ['customer' => $customer]) }}', '{{ 'customer_details_action_FUP' . $customer->id }}')">
                        FUP
                    </a>
                @endcan
                <a class="btn btn-outline-danger m-2"
                    href="{{ route('customers.customer_complains.create', ['customer' => $customer->id]) }}">
                    Add Complaint
                </a>
                <a class="btn btn-outline-info m-2"
                    href="{{ route('customers.internet-history.create', ['customer' => $customer->id]) }}">
                    Internet History <i class="fas fa-download"></i>
                </a>
                <a class="btn btn-outline-info m-2"
                    href="{{ route('customers.others-payments.create', ['customer' => $customer->id]) }}">
                    Other Payment
                </a>
                @can('disconnect', $customer)
                    <a class="btn btn-outline-danger m-2" id="{{ 'customer_details_action_Disconnect' . $customer->id }}"
                        href="#"
                        onclick="callUsersActionURL('{{ route('customers.disconnect.create', ['customer' => $customer]) }}', '{{ 'customer_details_action_Disconnect' . $customer->id }}')">
                        Disconnect
                    </a>
                @endcan
            @endif

        </div>

    </fieldset>
    {{-- Actions --}}
</div>
