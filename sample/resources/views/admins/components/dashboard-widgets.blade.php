{{-- Today's update --}}
<div class="card card-outline card-danger border-left border-danger">
    <div class="card-header">
        <h3 class="card-title font-italic"> Today's update ({{ date('l, d F Y') }})</h3>
    </div>
    <div class="card-body">
        {{-- First row --}}
        <div class="row">
            {{-- List Customers --}}
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box border border-info" onclick="window.open('{{ route('customers.index') }}', '_blank')">
                    <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">List Customers</span>
                    </div>
                </div>
            </div>
            {{-- Operator List --}}
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box border border-primary" onclick="window.open('{{ route('sub_operators.index') }}', '_blank')">
                    <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Operator List</span>
                    </div>
                </div>
            </div>
            {{-- Recharge Card --}}
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box border border-success" onclick="window.open('{{ route('recharge_cards.index') }}', '_blank')">
                    <span class="info-box-icon bg-success"><i class="fas fa-credit-card"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recharge Card</span>
                    </div>
                </div>
            </div>
            {{-- Operators Income --}}
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box border border-warning" onclick="window.open('{{ route('operators_incomes.index') }}', '_blank')">
                    <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Operators Income</span>
                    </div>
                </div>
            </div>
            {{-- Will be suspended --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-dark" onclick="window.location.href='{{ route('customers.index', ['will_be_suspended' => 1]) }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="will_be_suspended">0</h3>
                            <p>Customers will be suspended</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
            </div>
            {{-- Amount to be collected --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-dark" onclick="window.location.href='{{ route('customer_bills.index', ['due_date' => date(config('app.date_format'))]) }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="amount_to_be_collected">0</h3>
                            <p>Amount to be collected</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            {{-- Collected Amount --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-dark" onclick="window.location.href='{{ route('customer_payments.index', ['date' => date(config('app.date_format'))]) }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="collected_amount">0</h3>
                            <p>Collected Amount</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                </div>
            </div>
            {{-- SMS Sent --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-dark" onclick="window.location.href='{{ route('sms_histories.index') }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="today_sms_sent">0</h3>
                            <p>SMS Sent</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sms"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- First row --}}
    </div>
</div>
{{-- Today's update --}}

{{-- widgets --}}
<div class="card card-outline card-info border-left border-info">
    <div class="card-body">
        {{-- Second row --}}
        <div class="row">
            {{-- Total online Customers --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-primary" onclick="window.location.href='{{ route('customers.index', ['status' => 'active']) }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="online_customers">0</h3>
                            <p>Online Customers</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-globe"></i>
                    </div>
                </div>
            </div>
            {{-- Total Suspended Customers --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-primary" onclick="window.location.href='{{ route('customers.index', ['status' => 'suspended']) }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="suspended_customers">0</h3>
                            <p>Suspended Customers</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sort-down"></i>
                    </div>
                </div>
            </div>
            {{-- Total active Customers --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-primary" onclick="window.location.href='{{ route('customers.index', ['status' => 'active']) }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="active_customers">0</h3>
                            <p>Active Customers</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="far fa-thumbs-up"></i>
                    </div>
                </div>
            </div>
            {{-- Total disabled Customers --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-primary" onclick="window.location.href='{{ route('customers.index', ['status' => 'disabled']) }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="disabled_customers">0</h3>
                            <p>Disabled Customers</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="far fa-thumbs-down"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- Second row --}}
        {{-- Third row --}}
        <div class="row">
            {{-- Customer Complaints --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-navy" onclick="window.location.href='{{ route('customer_complains.index') }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="customer_complaints">0</h3>
                            <p>Customer Complaints</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
            </div>
            {{-- Total Paid Customers --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-navy" onclick="window.location.href='{{ route('customers.index', ['payment_status' => 'paid']) }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="paid_customers">0</h3>
                            <p>Paid Customers</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            {{-- Total Billed Customers --}}
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-navy" onclick="window.location.href='{{ route('customers.index', ['payment_status' => 'billed']) }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="billed_customers">0</h3>
                            <p>Billed Customers</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                </div>
            </div>
            {{-- SMS Balance --}}
            @if (Auth::user()->role == 'operator' || Auth::user()->role == 'sub_operator')
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="info-box bg-navy" onclick="window.location.href='{{ route('advance_sms_payments.create') }}'">
                    <div class="info-box-content">
                        <div class="inner">
                            <h3 id="account_balance">
                                @if (Auth::user()->account_type == 'credit')
                                {{ Auth::user()->credit_balance }}
                                @else
                                {{ Auth::user()->account_balance }}
                                @endif
                            </h3>
                            <p>Account Balance</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                </div>
            </div>
            @endif
        </div>
        {{-- Third row --}}
    </div>
</div>
{{-- widgets --}}
