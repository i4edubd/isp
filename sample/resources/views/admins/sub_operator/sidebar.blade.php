<aside class="main-sidebar {{ config('consumer.app_skin') }} elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ Auth::user()->company }}</span>
    </a>
    <!--/Brand Logo -->

    <!-- Sidebar -->
    <div class="sidebar">

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
                        '20' => 0,
                    ];

                    $link = [
                        '0' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '1' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '2' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '3' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '4' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0],
                        '5' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0],
                        '6' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '7' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '8' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '9' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '10' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '11' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '12' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '13' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '14' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '20' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                    ];

                    if (isset($active_menu)) {
                        $menu[$active_menu] = 1;
                    }

                    if (isset($active_link)) {
                        $link[$active_menu][$active_link] = 1;
                    }

                @endphp
                <!-- Add Search Box -->
<li class="nav-item">
    <form class="form-inline">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-sidebar" id="global-customer-search" type="search"
                placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-sidebar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</li>
<!--/Add Search Box-->

                <!--Dashboard menu[0]-->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link @if ($menu['0']) active @endif ">
                        <i class="fas fa-palette"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!--/Dashboard-->

                <!--Managers menu[1]-->
                <li class="nav-item">
                    <a href="{{ route('managers.index') }}"
                        class="nav-link @if ($menu['1']) active @endif ">
                        <i class="fas fa-users-cog"></i>
                        <p>Managers</p>
                    </a>
                </li>
                <!--/Managers-->

                <!--Packages menu[2]-->
                <li class="nav-item">
                    <a href="{{ route('packages.index') }}"
                        class="nav-link @if ($menu['2']) active @endif ">
                        <i class="fas fa-store"></i>
                        <p>Packages</p>
                    </a>
                </li>
                <!--/Packages-->

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

                <!--Accounts menus[3]-->
                <li class="nav-item has-treeview @if ($menu['3']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['3']) active @endif ">
                        <i class="far fa-envelope"></i>
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
                                <i class="far fa-circle nav-icon"></i>
                                <p>Accounts Receivable</p>
                            </a>
                        </li>
                        <!--/Accounts Receivable-->

                        <!--Accounts Payable-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.payable') }}"
                                class="nav-link @if ($link['3']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Accounts Payable</p>
                            </a>
                        </li>
                        <!--/Accounts Payable-->

                        <!--Daily Report-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.daily-report') }}"
                                class="nav-link @if ($link['13']['5']) active @endif ">
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

                    </ul>

                </li>
                <!--/Accounts menu[3]-->


                <!--Customers menus[4]-->
                <li class="nav-item has-treeview @if ($menu['4']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['4']) active @endif ">
                        <i class="far fa-user-circle"></i>
                        <p>
                            Customers
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--New Customers-->
                        <li class="nav-item">
                            <a href="{{ route('temp_customers.create') }}"
                                class="nav-link @if ($link['4']['0']) active @endif ">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>New Customer</p>
                            </a>
                        </li>
                        <!--/New Customers-->

                        <!--Customers-->
                        <li class="nav-item">
                            <a href="{{ route('customers.index') }}"
                                class="nav-link @if ($link['4']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>All Customers</p>
                            </a>
                        </li>
                        <!--/Customers-->

                        <!--Online Customers-->
                        <li class="nav-item">
                            <a href="{{ route('online_customers.index') }}"
                                class="nav-link @if ($link['4']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Online Customers</p>
                            </a>
                        </li>
                        <!--/Online Customers-->

                        <!--Inactive Customers-->
                        <li class="nav-item">
                            <a href="{{ route('offline_customers.index') }}"
                                class="nav-link @if ($link['4']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Offline Customers</p>
                            </a>
                        </li>
                        <!--/Inactive Customers-->

                        <!--Customer zone-->
                        <li class="nav-item">
                            <a href="{{ route('customer_zones.index') }}"
                                class="nav-link @if ($link['4']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Customer zone</p>
                            </a>
                        </li>
                        <!--/Customer zone-->

                        <!--devices-->
                        <li class="nav-item">
                            <a href="{{ route('devices.index') }}"
                                class="nav-link @if ($link['4']['7']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Devices</p>
                            </a>
                        </li>
                        <!--/devices-->

                        <!--Custom Field-->
                        <li class="nav-item">
                            <a href="{{ route('custom_fields.index') }}"
                                class="nav-link @if ($link['4']['6']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Custom Field</p>
                            </a>
                        </li>
                        <!--/Custom Field-->

                    </ul>

                </li>
                <!--/Customers menu[4]-->

                <!--Bills and Payments[5]-->
                <li class="nav-item has-treeview @if ($menu['5']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['5']) active @endif ">
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
                                class="nav-link @if ($link['5']['6']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>ISP/Biller</p>
                            </a>
                        </li>
                        <!--/ISP Information-->

                        <!--Billing Profile -->
                        <li class="nav-item">
                            <a href="{{ route('billing_profiles.index') }}"
                                class="nav-link @if ($link['5']['7']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Billing Profile</p>
                            </a>
                        </li>
                        <!--/Billing Profile-->

                        <!--Customer payments-->
                        <li class="nav-item">
                            <a href="{{ route('customer_payments.index') }}"
                                class="nav-link @if ($link['5']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Payments</p>
                            </a>
                        </li>
                        <!--/Customer payments-->

                        <!--Verify Payments-->
                        <li class="nav-item">
                            <a href="{{ route('verify-send-money.index') }}"
                                class="nav-link @if ($link['5']['8']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Verify Payments</p>
                            </a>
                        </li>
                        <!--Verify Payments-->

                        <!--Bills-->
                        <li class="nav-item">
                            <a href="{{ route('customer_bills.index') }}"
                                class="nav-link @if ($link['5']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Bills</p>
                            </a>
                        </li>
                        <!--Bills-->

                        <!--Bills Summary-->
                        <li class="nav-item">
                            <a href="{{ route('customer_bills_summary.index') }}"
                                class="nav-link @if ($link['5']['9']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Bills Summary</p>
                            </a>
                        </li>
                        <!--Bills Summary-->

                        <!--Due Notifier-->
                        <li class="nav-item">
                            <a href="{{ route('due_date_reminders.index') }}"
                                class="nav-link @if ($link['5']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Due Notifier</p>
                            </a>
                        </li>
                        <!--Due Notifier-->

                        <!--Expiration Notifier-->
                        <li class="nav-item">
                            <a href="{{ route('expiration_notifiers.index') }}"
                                class="nav-link @if ($link['5']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Expiration Notifier</p>
                            </a>
                        </li>
                        <!--Expiration Notifier-->

                        <!--Payment Link Broadcast-->
                        <li class="nav-item">
                            <a href="{{ route('payment-link-broadcast.create') }}"
                                class="nav-link @if ($link['5']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Payment Link Broadcast</p>
                            </a>
                        </li>
                        <!--Payment Link Broadcast-->

                    </ul>

                </li>
                <!--/Customer Online payments[5]-->

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


                <!--Expenses[6]-->

                <li class="nav-item has-treeview @if ($menu['6']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['6']) active @endif ">
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
                                class="nav-link @if ($link['6']['4']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Incomes</p>
                            </a>
                        </li>
                        <!--/Incomes-->

                        <!--Expenses-->
                        <li class="nav-item">
                            <a href="{{ route('expenses.index') }}"
                                class="nav-link @if ($link['6']['1']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Expenses</p>
                            </a>
                        </li>
                        <!--/Expenses-->

                        <!--Expense Category-->
                        <li class="nav-item">
                            <a href="{{ route('expense_categories.index') }}"
                                class="nav-link @if ($link['6']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Expense Category</p>
                            </a>
                        </li>
                        <!--Expense Category-->

                        <!--Expense Report-->
                        <li class="nav-item">
                            <a href="{{ route('expense.report') }}"
                                class="nav-link @if ($link['6']['3']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Expense Report</p>
                            </a>
                        </li>
                        <!--Expense Report-->

                        <!--Income Vs. Expense-->
                        <li class="nav-item">
                            <a href="{{ route('incomes-vs-expenses.index') }}"
                                class="nav-link @if ($link['6']['5']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Income Vs. Expense</p>
                            </a>
                        </li>
                        <!--Income Vs. Expense-->

                    </ul>

                </li>

                <!--/Expenses[6]-->


                <!--SMS menus[7]-->
                <li class="nav-item has-treeview @if ($menu['7']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['7']) active @endif ">
                        <i class="far fa-envelope"></i>
                        <p>
                            SMS
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        
                        <!--SMS Broadcast-->
                        <li class="nav-item">
                            <a href="{{ route('sms-broadcast-jobs.create') }}"
                                class="nav-link @if ($link['7']['4']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>SMS Broadcast</p>
                            </a>
                        </li>
                        <!--/SMS Broadcast-->

                        <!--SMS History-->
                        <li class="nav-item">
                            <a href="{{ route('sms_histories.index') }}"
                                class="nav-link @if ($link['7']['0']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>SMS History</p>
                            </a>
                        </li>
                        <!--/SMS History-->

                        <!--SMS Bills-->
                        <li class="nav-item">
                            <a href="{{ route('sms_bills.index') }}"
                                class="nav-link @if ($link['7']['1']) active @endif ">
                                <i class=""></i>
                                <i class="far fa-circle nav-icon"></i>
                                <p>SMS Bills</p>
                            </a>
                        </li>
                        <!--/SMS Bills-->

                        <!--SMS Payments-->
                        <li class="nav-item">
                            <a href="{{ route('sms_payments.index') }}"
                                class="nav-link @if ($link['7']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>SMS Payments</p>
                            </a>
                        </li>
                        <!--/SMS Payments-->

                        <!--SMS Controls-->
                        <li class="nav-item">
                            <a href="{{ route('event_sms.index') }}"
                                class="nav-link @if ($link['7']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>SMS Controls</p>
                            </a>
                        </li>
                        <!--/SMS Controls-->

                    </ul>

                </li>
                <!--/SMS menu[7]-->



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
                                <i class="far fa-circle nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                        <!--/Change Password-->

                        <!--Two Factor-->
                        <li class="nav-item">
                            <a href="{{ route('two-factor.show') }}"
                                class="nav-link @if ($link['8']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Two Factor</p>
                            </a>
                        </li>
                        <!--/Two Factor-->

                        <!--Device Verification-->
                        <li class="nav-item">
                            <a href="{{ route('operators.device-identification.index', ['operator' => Auth::user()]) }}"
                                class="nav-link @if ($link['8']['4']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Device Verification</p>
                            </a>
                        </li>
                        <!--/Device Verification-->

                        <!--activity_logs-->
                        <li class="nav-item">
                            <a href="{{ route('activity_logs.index') }}"
                                class="nav-link @if ($link['8']['3']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Activity Logs</p>
                            </a>
                        </li>
                        <!--/activity_logs-->

                        <!--Authentication logs-->
                        <li class="nav-item">
                            <a href="{{ route('authentication_log.index') }}"
                                class="nav-link @if ($link['8']['5']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Authentication logs</p>
                            </a>
                        </li>
                        <!--/Authentication logs-->

                    </ul>

                </li>
                <!--/Security[8]-->

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
         <!-- Include global search result modal -->
        @include('laraview.layouts.global_search_result_modal')
        <!-- /Include global search result modal -->

    </div>
    <!-- /sidebar -->

</aside>
