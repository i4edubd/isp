<aside class="main-sidebar {{ config('consumer.app_skin') }} elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ Auth::user()->company }}</span>
    </a>
    <!--/Brand Logo -->

    <!-- Sidebar -->
    <div class="sidebar">
         <!-- Add Search Box -->
        <li class="nav-item">
            <form class="form-inline">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" id="global-customer-search" type="search"
                        placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </li>
        <!--/Add Search Box-->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                @php

                    $menu = [
                        '0' => 0,
                        '1' => 0,
                        '2' => 0,
                        '3' => 0,
                        '4' => 0,
                        '5' => 0,
                        '6' => 0,
                        '7' => 0,
                        '8' => 0,
                        '9' => 0,
                        '10' => 0,
                        '11' => 0,
                        '12' => 0,
                        '13' => 0,
                        '14' => 0,
                        '15' => 0,
                        '20' => 0,
                        '30' => 0,
                        '40' => 0,
                    ];

                    $link = [
                        '0' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '1' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0],
                        '2' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0],
                        '3' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0],
                        '4' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0],
                        '5' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0],
                        '6' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0],
                        '7' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '8' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '9' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '10' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '11' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '12' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '13' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '14' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '15' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '20' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '30' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '40' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                    ];

                    if (isset($active_menu)) {
                        $menu[$active_menu] = 1;
                    }

                    if (isset($active_link)) {
                        $link[$active_menu][$active_link] = 1;
                    }

                @endphp
                
                <!--Dashboard menu[0]-->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link @if ($menu['0']) active @endif ">
                        <i class="fas fa-palette"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!--/Dashboard-->

                <!--Resellers & Managers menus[1]-->
                <li class="nav-item has-treeview @if ($menu['1']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['1']) active @endif ">
                        <i class="fas fa-users-cog"></i>
                        <p>
                            Resellers & Managers
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Operators-->
                        <li class="nav-item">
                            <a href="{{ route('operators.index') }}"
                                class="nav-link @if ($link['1']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Resellers/Operators</p>
                            </a>
                        </li>
                        <!--/Operators-->

                        <!--Sub-Resellers-->
                        <li class="nav-item">
                            <a href="{{ route('sub_operators.index') }}"
                                class="nav-link @if ($link['1']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Sub-Resellers</p>
                            </a>
                        </li>
                        <!--/Sub-Resellers-->

                        <!--Managers-->
                        <li class="nav-item">
                            <a href="{{ route('managers.index') }}"
                                class="nav-link @if ($link['1']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Managers</p>
                            </a>
                        </li>
                        <!--/Managers-->

                        <!--Operator and Sub-Operator Payments-->
                        <li class="nav-item">
                            <a href="{{ route('operator_suboperator_payments.index') }}"
                                class="nav-link @if ($link['1']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Operators Payments</p>
                            </a>
                        </li>
                        <!--Operator and Sub-Operator Payments-->

                        <!--notice broadcast-->
                        <li class="nav-item">
                            <a href="{{ route('operators-notice-broadcast.create') }}"
                                class="nav-link @if ($link['1']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Notice Broadcast</p>
                            </a>
                        </li>
                        <!--/notice broadcast-->

                    </ul>

                </li>
                <!--/Operators & Managers menu[1]-->


                <!--Routers & Packages menus[2]-->
                <li class="nav-item has-treeview @if ($menu['2']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['2']) active @endif ">
                        <i class="fas fa-asterisk"></i>
                        <p>
                            Network & Packages
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--VPN Accounts-->
                        @can('viewAny', App\Models\vpn_account::class)
                            <li class="nav-item">
                                <a href="{{ route('vpn_accounts.index') }}"
                                    class="nav-link @if ($link['2']['8']) active @endif ">
                                    <i class="fas fa-shield-alt nav-icon"></i>
                                    <p>
                                        VPN Accounts
                                    </p>
                                </a>
                            </li>
                        @endcan
                        <!--/VPN Accounts-->

                        <!--Routers-->
                        <li class="nav-item">
                            <a href="{{ route('routers.index') }}"
                                class="nav-link @if ($link['2']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Routers</p>
                            </a>
                        </li>
                        <!--/Routers-->

                        <!--Device Monitoring-->
                        <li class="nav-item">
                            <a href="{{ route('device-monitors.index') }}"
                                class="nav-link @if ($link['2']['10']) active @endif ">
                                <i class="fas fa-network-wired nav-icon"></i>
                                <p>Device Monitoring</p>
                            </a>
                        </li>
                        <!--/Device Monitoring-->

                        <!--Backup Settings-->
                        <li class="nav-item">
                            <a href="{{ route('backup_settings.index') }}"
                                class="nav-link @if ($link['2']['9']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Backup & Auth Settings</p>
                            </a>
                        </li>
                        <!--/Backup Settings-->

                        <!--IPv4 Pool-->
                        <li class="nav-item">
                            <a href="{{ route('ipv4pools.index') }}"
                                class="nav-link @if ($link['2']['2']) active @endif ">
                                <i class="fas fa-angle-right text-danger nav-icon"></i>
                                <p>IPv4 Pools</p>
                            </a>
                        </li>
                        <!--/IPv4 Pool-->

                        <!--IPv6 Pools-->
                        <li class="nav-item">
                            <a href="{{ route('ipv6pools.index') }}"
                                class="nav-link @if ($link['2']['3']) active @endif ">
                                <i class="fas fa-angle-right text-danger nav-icon"></i>
                                <p>IPv6 Pools</p>
                            </a>
                        </li>
                        <!--/IPv6 Pools-->

                        <!--PPPoE Profile -->
                        <li class="nav-item">
                            <a href="{{ route('pppoe_profiles.index') }}"
                                class="nav-link @if ($link['2']['4']) active @endif ">
                                <i class="fas fa-angle-right text-danger nav-icon"></i>
                                <p>PPP/Profiles</p>
                            </a>
                        </li>
                        <!--/PPPoE Profile-->

                        <!--Billing Profile -->
                        <li class="nav-item">
                            <a href="{{ route('billing_profiles.index') }}"
                                class="nav-link @if ($link['2']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Billing Profile</p>
                            </a>
                        </li>
                        <!--/Billing Profile-->

                        <!--Master packages-->
                        <li class="nav-item">
                            <a href="{{ route('master_packages.index') }}"
                                class="nav-link @if ($link['2']['6']) active @endif ">
                                <i class="fas fa-angle-right text-danger nav-icon"></i>
                                <p>All Packages</p>
                            </a>
                        </li>
                        <!--/Master packages-->

                        <!--packages-->
                        <li class="nav-item">
                            <a href="{{ route('packages.index') }}"
                                class="nav-link @if ($link['2']['7']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Own Packages</p>
                            </a>
                        </li>
                        <!--/packages-->
                    </ul>
                    </li>
                <!--/Routers & Packages menu[2]-->

                <!--Customers menus[5]-->
                <li class="nav-item has-treeview @if ($menu['5']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['5']) active @endif ">
                        <i class="far fa-user-circle"></i>
                        <p>
                            All Customers
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--New Customers-->
                        <li class="nav-item">
                            <a href="{{ route('temp_customers.create') }}"
                                class="nav-link @if ($link['5']['0']) active @endif ">
                                <i class="fas fa-plus nav-icon nav-icon"></i>
                                <p>New Customer</p>
                            </a>
                        </li>
                        <!--/New Customers-->

                        <!--Customers-->
                        <li class="nav-item">
                            <a href="{{ route('customers.index') }}"
                                class="nav-link @if ($link['5']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>All Customers</p>
                            </a>
                        </li>
                        <!--/Customers-->

                        <!--Online Customers-->
                        <li class="nav-item">
                            <a href="{{ route('online_customers.index') }}"
                                class="nav-link @if ($link['5']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Online Customers</p>
                            </a>
                        </li>
                        <!--/Online Customers-->

                        <!--Import Customers-->
                        <li class="nav-item">
                            <a href="{{ route('pppoe_customers_import.index') }}"
                                class="nav-link @if ($link['5']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Import PPP Customers</p>
                            </a>
                        </li>
                        <!--/Import Customers-->

                        <!--Customer zone-->
                        <li class="nav-item">
                            <a href="{{ route('customer_zones.index') }}"
                                class="nav-link @if ($link['5']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Customer zone</p>
                            </a>
                        </li>
                        <!--/Customer zone-->

                        <!--devices-->
                        <li class="nav-item">
                            <a href="{{ route('devices.index') }}"
                                class="nav-link @if ($link['5']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Devices</p>
                            </a>
                        </li>
                        <!--/devices-->

                        <!--Custom Field-->
                        <li class="nav-item">
                            <a href="{{ route('custom_fields.index') }}"
                                class="nav-link @if ($link['5']['6']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Custom Field</p>
                            </a>
                        </li>
                        <!--/Custom Field-->

                        <!--BTRC Report-->
                        <li class="nav-item">
                            <a href="{{ route('btrc-report.create') }}"
                                class="nav-link @if ($link['5']['7']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>BTRC Report</p>
                            </a>
                        </li>
                        <!--/BTRC Report-->

                        <!--Deleted Customers-->
                        <li class="nav-item">
                            <a href="{{ route('deleted_customers.index') }}"
                                class="nav-link @if ($link['5']['8']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Deleted Customers</p>
                            </a>
                        </li>
                        <!--/Deleted Customers-->

                    </ul>

                </li>
                <!--/Customers menu[5]-->

                <!--Recharge Card [13]-->
                <li class="nav-item has-treeview @if ($menu['13']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['13']) active @endif ">
                        <i class="fas fa-store"></i>
                        <p>
                            Recharge Card
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Card Distributors-->
                        <li class="nav-item">
                            <a href="{{ route('card_distributors.index') }}"
                                class="nav-link @if ($link['13']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Card Distributors</p>
                            </a>
                        </li>
                        <!--/Card Distributors-->

                        <!--Distributors Payments-->
                        <li class="nav-item">
                            <a href="{{ route('distributor_payments.index') }}"
                                class="nav-link @if ($link['13']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Distributors Payments</p>
                            </a>
                        </li>
                        <!--/Distributors Payments-->

                        <!--Recharge Cards-->
                        <li class="nav-item">
                            <a href="{{ route('recharge_cards.index') }}"
                                class="nav-link @if ($link['13']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Recharge Cards</p>
                            </a>
                        </li>
                        <!--/Recharge Cards-->

                    </ul>

                </li>
                <!--/Recharge Card[13]-->


                <!--Bills and Payments[6]-->
                <li class="nav-item has-treeview @if ($menu['6']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['6']) active @endif ">
                        <i class="fas fa-parking"></i>
                        <p>
                            Bills and Payments
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--ISP Information-->
                        <li class="nav-item">
                            <a href="{{ route('operators.profile.index', ['operator' => Auth::user()->id]) }}"
                                class="nav-link @if ($link['6']['6']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>ISP Information</p>
                            </a>
                        </li>
                        <!--/ISP Information-->

                        <!--Verify Payments-->
                        <li class="nav-item">
                            <a href="{{ route('verify-send-money.index') }}"
                                class="nav-link @if ($link['6']['8']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Verify Payments</p>
                            </a>
                        </li>
                        <!--Verify Payments-->

                        <!--Bills-->
                        <li class="nav-item">
                            <a href="{{ route('customer_bills.index') }}"
                                class="nav-link @if ($link['6']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Bills</p>
                            </a>
                        </li>
                        <!--Bills-->

                        <!--Bills Summary-->
                        <li class="nav-item">
                            <a href="{{ route('customer_bills_summary.index') }}"
                                class="nav-link @if ($link['6']['9']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Billing Summary</p>
                            </a>
                        </li>
                        <!--Bills Summary-->

                        <!--Due Notifier-->
                        <li class="nav-item">
                            <a href="{{ route('due_date_reminders.index') }}"
                                class="nav-link @if ($link['6']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Due Notifier</p>
                            </a>
                        </li>
                        <!--Due Notifier-->

                        <!--Expiration Notifier-->
                        <li class="nav-item">
                            <a href="{{ route('expiration_notifiers.index') }}"
                                class="nav-link @if ($link['6']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Expiration Notifier</p>
                            </a>
                        </li>
                        <!--Expiration Notifier-->

                        <!--Payment Link Broadcast-->
                        <li class="nav-item">
                            <a href="{{ route('payment-link-broadcast.create') }}"
                                class="nav-link @if ($link['6']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Payment Link Broadcast</p>
                            </a>
                        </li>
                        <!--Payment Link Broadcast-->


                    </ul>

                </li>
                <!--/Bills and Payments[6]-->

                <!--Accounts menus[3]-->
                <li class="nav-item has-treeview @if ($menu['3']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['3']) active @endif ">
                        <i class="fas fa-money-check-alt"></i>
                        <p>
                            Accounts
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Accounts Receivable-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.receivable') }}"
                                class="nav-link @if ($link['3']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>{{ __('Accounts Receivable') }}</p>
                            </a>
                        </li>
                        <!--/Accounts Receivable-->

                        <!--Accounts Payable-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.payable') }}"
                                class="nav-link @if ($link['3']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Accounts Payable</p>
                            </a>
                        </li>
                        <!--/Accounts Payable-->

                        <!--Daily Report-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.daily-report') }}"
                                class="nav-link @if ($link['3']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Daily Report</p>
                            </a>
                        </li>
                        <!--/Daily Report-->

                        <!--Monthly Report-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.monthly-report') }}"
                                class="nav-link @if ($link['3']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Monthly Report</p>
                            </a>
                        </li>
                        <!--/Monthly Report-->

                        <!--Operator and Sub-Operator Payments-->
                        <li class="nav-item">
                            <a href="{{ route('operator_suboperator_payments.index') }}"
                                class="nav-link @if ($link['3']['6']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Operators Payments</p>
                            </a>
                        </li>
                        <!--Operator and Sub-Operator Payments-->
                        
                        <!--Customer payments-->
                        <li class="nav-item">
                            <a href="{{ route('customer_payments.index') }}"
                                class="nav-link @if ($link['3']['7']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Customer Payments</p>
                            </a>
                        </li>
                        <!--/Customer payments-->

                        <!--Distributors Payments-->
                        <li class="nav-item">
                            <a href="{{ route('distributor_payments.index') }}"
                                class="nav-link @if ($link['3']['8']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Distributors Payments</p>
                            </a>
                        </li>
                        <!--/Distributors Payments-->

                    </ul>

                </li>
                <!--/Accounts menu[3]-->


                <!--Expenses[7]-->
                <li class="nav-item has-treeview @if ($menu['7']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['7']) active @endif ">
                        <i class="far fa-minus-square"></i>
                        <p>
                            Incomes & Expenses
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Incomes-->
                        <li class="nav-item">
                            <a href="{{ route('operators_incomes.index') }}"
                                class="nav-link @if ($link['7']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Incomes</p>
                            </a>
                        </li>
                        <!--/Incomes-->

                        <!--Expenses-->
                        <li class="nav-item">
                            <a href="{{ route('expenses.index') }}"
                                class="nav-link @if ($link['7']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Expenses</p>
                            </a>
                        </li>
                        <!--/Expenses-->

                        <!--Expense Category-->
                        <li class="nav-item">
                            <a href="{{ route('expense_categories.index') }}"
                                class="nav-link @if ($link['7']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Expense Category</p>
                            </a>
                        </li>
                        <!--Expense Category-->

                        <!--Expense Report-->
                        <li class="nav-item">
                            <a href="{{ route('expense.report') }}"
                                class="nav-link @if ($link['7']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Expense Report</p>
                            </a>
                        </li>
                        <!--Expense Report-->

                        <!--Income Vs.Expense-->
                        <li class="nav-item">
                            <a href="{{ route('incomes-vs-expenses.index') }}"
                                class="nav-link @if ($link['7']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Income Vs. Expense</p>
                            </a>
                        </li>
                        <!--Income Vs.Expense-->

                    </ul>

                </li>
                <!--/Expenses[7]-->

                <!--SMS menus[10]-->
                <li class="nav-item has-treeview @if ($menu['10']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['10']) active @endif ">
                        <i class="far fa-envelope"></i>
                        <p>
                            SMS
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Send SMS-->
                        <li class="nav-item">
                            <a href="{{ route('sms_histories.create') }}"
                                class="nav-link @if ($link['10']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Send SMS</p>
                            </a>
                        </li>
                        <!--/Send SMS-->

                        <!--SMS Broadcast-->
                        <li class="nav-item">
                            <a href="{{ route('sms-broadcast-jobs.create') }}"
                                class="nav-link @if ($link['10']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>SMS Broadcast</p>
                            </a>
                        </li>
                        <!--/SMS Broadcast-->

                        <!--SMS History-->
                        <li class="nav-item">
                            <a href="{{ route('sms_histories.index') }}"
                                class="nav-link @if ($link['10']['0']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>SMS History</p>
                            </a>
                        </li>
                        <!--/SMS History-->

                        <!--SMS Bills-->
                        <li class="nav-item">
                            <a href="{{ route('sms_bills.index') }}"
                                class="nav-link @if ($link['10']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>SMS Bills</p>
                            </a>
                        </li>
                        <!--/SMS Bills-->

                        <!--SMS Payments-->
                        <li class="nav-item">
                            <a href="{{ route('sms_payments.index') }}"
                                class="nav-link @if ($link['10']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>SMS Payments</p>
                            </a>
                        </li>
                        <!--/SMS Payments-->

                        <!--Due Notifier-->
                        <li class="nav-item">
                            <a href="{{ route('due_date_reminders.index') }}"
                                class="nav-link @if ($link['6']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Due Notifier</p>
                            </a>
                        </li>
                        <!--Due Notifier-->

                        <!--Expiration Notifier-->
                        <li class="nav-item">
                            <a href="{{ route('expiration_notifiers.index') }}"
                                class="nav-link @if ($link['6']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Expiration Notifier</p>
                            </a>
                        </li>
                        <!--Expiration Notifier-->

                        <!--Payment Link Broadcast-->
                        <li class="nav-item">
                            <a href="{{ route('payment-link-broadcast.create') }}"
                                class="nav-link @if ($link['6']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Payment Link Broadcast</p>
                            </a>
                        </li>
                        <!--Payment Link Broadcast-->

                        <!--SMS Controls-->
                        <li class="nav-item">
                            <a href="{{ route('event_sms.index') }}"
                                class="nav-link @if ($link['10']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>SMS Settings</p>
                            </a>
                        </li>
                        <!--/SMS Controls-->

                    </ul>

                </li>
                <!--/SMS menu[10]-->


                <!--complaint-management[20]-->
                <li class="nav-item has-treeview @if ($menu['20']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['20']) active @endif ">
                        <i class="fas fa-comments"></i>
                        <p>
                            Complaint Management
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--departments-->
                        <li class="nav-item">
                            <a href="{{ route('departments.index') }}"
                                class="nav-link @if ($link['20']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Departments</p>
                            </a>
                        </li>
                        <!--/departments-->

                        <!--complain_categories-->
                        <li class="nav-item">
                            <a href="{{ route('complain_categories.index') }}"
                                class="nav-link @if ($link['20']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Complain Categories</p>
                            </a>
                        </li>
                        <!--complain_categories-->

                        <!--customer_complains-->
                        <li class="nav-item">
                            <a href="{{ route('customer_complains.index') }}"
                                class="nav-link @if ($link['20']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Complaints</p>
                            </a>
                        </li>
                        <!--customer_complains-->

                        <!--Archived Complaints-->
                        <li class="nav-item">
                            <a href="{{ route('archived_customer_complains.index') }}"
                                class="nav-link @if ($link['20']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Archived Complaints</p>
                            </a>
                        </li>
                        <!--Archived Complaints-->

                        <!--Reporting-->
                        <li class="nav-item">
                            <a href="{{ route('complaint-reporting.index') }}"
                                class="nav-link @if ($link['20']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Reporting</p>
                            </a>
                        </li>
                        <!--Reporting-->

                    </ul>

                </li>
                <!--/complaint-management[20]-->


                <!--Security [8]-->
                <li class="nav-item has-treeview @if ($menu['8']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['8']) active @endif ">
                        <i class="fas fa-key"></i>
                        <p>
                            Security
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Change Password-->
                        <li class="nav-item">
                            <a href="{{ route('admin.password.change') }}"
                                class="nav-link @if ($link['8']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                        <!--/Change Password-->

                        <!--Two Factor-->
                        <li class="nav-item">
                            <a href="{{ route('two-factor.show') }}"
                                class="nav-link @if ($link['8']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Two Factor</p>
                            </a>
                        </li>
                        <!--/Two Factor-->

                        <!--Device Verification-->
                        <li class="nav-item">
                            <a href="{{ route('operators.device-identification.index', ['operator' => Auth::user()]) }}"
                                class="nav-link @if ($link['8']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Device Verification</p>
                            </a>
                        </li>
                        <!--/Device Verification-->

                        <!--activity_logs-->
                        <li class="nav-item">
                            <a href="{{ route('activity_logs.index') }}"
                                class="nav-link @if ($link['8']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Activity Logs</p>
                            </a>
                        </li>
                        <!--/activity_logs-->

                        <!--activity_logs-->
                        <li class="nav-item">
                            <a href="{{ route('authentication_log.index') }}"
                                class="nav-link @if ($link['8']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Authentication Logs</p>
                            </a>
                        </li>
                        <!--/activity_logs-->

                    </ul>

                </li>
                <!--/Security[8]-->


                <!--Subscription menu[9]-->
                <li class="nav-item has-treeview @if ($menu['9']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['9']) active @endif ">
                        <i class="far fa-clock"></i>
                        <p>
                            Subscriptions
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Subscription Bills-->
                        <li class="nav-item">
                            <a href="{{ route('subscription_bills.index') }}"
                                class="nav-link @if ($link['9']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Subscription Bills</p>
                            </a>
                        </li>
                        <!--/Subscription Bills-->

                        <!--Subscription Payments-->
                        <li class="nav-item">
                            <a href="#"
                                class="nav-link @if ($link['9']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Subscription Payments</p>
                            </a>
                        </li>
                        <!--/Subscription Payments-->

                        <!--Pricing-->
                        <li class="nav-item">
                            <a href="https://ispbills.com/pricing/"
                                class="nav-link @if ($link['9']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Pricing</p>
                            </a>
                        </li>
                        <!--/Pricing-->

                    </ul>

                </li>
                <!--/Subscription menu[9]-->

                <!--Hotspot HTML menu[11] -->
                <li class="nav-item">
                    <a href="/storage/hotspot.zip" class="nav-link">
                        <i class="fas fa-download"></i>
                        <p>Hotspot HTML</p>
                    </a>
                </li>
                <!--/hotspot HTML-->

                <!--Help menu[12]-->
                @if (config('consumer.help_menu'))

                    <li class="nav-item has-treeview @if ($menu['12']) menu-open @endif ">

                        <a href="#" class="nav-link @if ($menu['12']) active @endif ">
                            <i class="far fa-question-circle"></i>
                            <p>
                                Help
                                <i class="fas fa-caret-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            <!--Questions & Answers-->
                            <li class="nav-item">
                                <a href="{{ route('exam.index', ['show' => 0]) }}"
                                    class="nav-link @if ($link['12']['4']) active @endif ">
                                    <i class="fas fa-question nav-icon"></i>
                                    <p>Questions & Answers</p>
                                </a>
                            </li>
                            <!--/Questions & Answers-->

                            <!--Video Tutorials-->
                            <li class="nav-item">
                                <a href="#" class="nav-link @if ($link['12']['3']) active @endif ">
                                    <i class="fas fa-video nav-icon"></i>
                                    <p>Video Tutorials</p>
                                </a>
                            </li>
                            <!--/Video Tutorials-->

                            <!--Documentation-->
                            <li class="nav-item">
                                <a href="{{ config('app.doc_url') }}"
                                    class="nav-link @if ($link['12']['1']) active @endif ">
                                    <i class="fas fa-book nav-icon"></i>
                                    <p>Documentation/FAQ</p>
                                </a>
                            </li>
                            <!--/Documentation-->

                            <!--helpline-->
                            @if (config('consumer.help_menu'))
                                <li class="nav-item">
                                    <a href="{{ route('helpline') }}"
                                        class="nav-link @if ($link['12']['2']) active @endif ">
                                        <i class="fas fa-phone-volume nav-icon"></i>
                                        <p>Helpline</p>
                                    </a>
                                </li>
                            @endif
                            <!--/helpline-->

                        </ul>

                    </li>

                @endif
                <!--/Help menu[12]-->

                <!--Affiliate Program[30]-->
                @can('enrolInSupportProgramme')
                    <li class="nav-item has-treeview @if ($menu['30']) menu-open @endif ">

                        <a href="#" class="nav-link @if ($menu['30']) active @endif ">
                            <i class="fas fa-money-bill-alt"></i>
                            <p>
                                Affiliate Program
                                <i class="fas fa-caret-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            <!--Program Policy-->
                            <li class="nav-item">
                                <a href="{{ route('support_programme_policy.index') }}"
                                    class="nav-link @if ($link['30']['1']) active @endif ">
                                    <i class="fas fa-info-circle nav-icon"></i>
                                    <p>
                                        Program Policy
                                    </p>
                                </a>
                            </li>
                            <!--/Program Policy-->

                            <!--Affiliate Link-->
                            <li class="nav-item">
                                <a href="{{ route('affiliate_link.index') }}"
                                    class="nav-link @if ($link['30']['2']) active @endif ">
                                    <i class="fas fa-link nav-icon"></i>
                                    <p>Affiliate Link</p>
                                </a>
                            </li>
                            <!--/Affiliate Link-->

                            <!--Leads-->
                            <li class="nav-item">
                                <a href="{{ route('affiliate-leads.index') }}"
                                    class="nav-link @if ($link['30']['3']) active @endif ">
                                    <i class="fas fa-circle nav-icon"></i>
                                    <p>Leads</p>
                                </a>
                            </li>
                            <!--/Leads-->

                            <!--Sales-->
                            <li class="nav-item">
                                <a href="{{ route('support_programme_sales.index') }}"
                                    class="nav-link @if ($link['30']['4']) active @endif ">
                                    <i class="fas fa-dollar-sign nav-icon"></i>
                                    <p>Sales</p>
                                </a>
                            </li>
                            <!--/Sales-->

                        </ul>

                    </li>
                @endcan
                <!--/Affiliate Program[30]-->

                <!--VAT [40]-->
                <li class="nav-item has-treeview @if ($menu['40']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['40']) active @endif ">
                        <i class="fas fa-percent"></i>
                        <p>
                            VAT
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--VAT profile-->
                        <li class="nav-item">
                            <a href="{{ route('vat_profiles.index') }}"
                                class="nav-link @if ($link['40']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>VAT Profile</p>
                            </a>
                        </li>
                        <!--/VAT profile-->

                        <!--VAT collections-->
                        <li class="nav-item">
                            <a href="{{ route('vat_collections.index') }}"
                                class="nav-link @if ($link['40']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>VAT collections</p>
                            </a>
                        </li>
                        <!--/VAT collections-->

                    </ul>

                </li>
                <!--/VAT [40]-->

                <!--Data Policy-->
                <li class="nav-item">
                    <a href="{{ route('data-policy.index') }}"
                        class="nav-link @if ($menu['14']) active @endif ">
                        <i class="fas fa-file-contract"></i>
                        <p>Data Policy</p>
                    </a>
                </li>
                <!--/Data Policy-->

            </ul>

        </nav>
        <!-- /sidebar-menu -->
                
    </div>
    <!-- /sidebar -->
    
    
</aside>
