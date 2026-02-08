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
                ];

                $link = [
                '0' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '1' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '2' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '3' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '4' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '5' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '6' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '7' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '8' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '9' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '10' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '11' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '12' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '13' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                ];

                if(isset($active_menu)){
                $menu[$active_menu] = 1;
                }

                if(isset($active_link)){
                $link[$active_menu][$active_link] = 1;
                }

                @endphp

                <!--Dashboard menu[0]-->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link @if ($menu['0']) active @endif ">
                        <i class="fas fa-palette"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!--/Dashboard-->

                <!--Group Admins menu[1] -->
                <li class="nav-item">
                    <a href="{{ route('group_admins.index') }}" class="nav-link @if ($menu['1']) active @endif ">
                        <i class="fas fa-users-cog"></i>
                        <p>Group Admins</p>
                    </a>
                </li>
                <!--/Group Admins-->


                <!--Accounts menus[2]-->
                <li class="nav-item has-treeview @if ($menu['2']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['2']) active @endif ">
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
                                class="nav-link @if ($link['2']['1']) active @endif ">
                                <i class=""></i>
                                <i class="far fa-circle nav-icon"></i>
                                <p>Accounts Receivable</p>
                            </a>
                        </li>
                        <!--/Accounts Receivable-->

                        <!--Accounts Payable-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.payable') }}"
                                class="nav-link @if ($link['2']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Accounts Payable</p>
                            </a>
                        </li>
                        <!--/Accounts Payable-->

                        <!--Pending Transactions-->
                        <li class="nav-item">
                            <a href="{{ route('pending_transactions.index') }}"
                                class="nav-link @if ($link['2']['3']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pending Transactions</p>
                            </a>
                        </li>
                        <!--/Pending Transactions-->

                        <!--Daily Report-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.daily-report') }}"
                                class="nav-link @if ($link['2']['5']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Daily Report</p>
                            </a>
                        </li>
                        <!--/Daily Report-->

                        <!--Monthly Report-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.monthly-report') }}"
                                class="nav-link @if ($link['2']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Monthly Report</p>
                            </a>
                        </li>
                        <!--/Monthly Report-->

                    </ul>

                </li>
                <!--/Accounts menu[4]-->


                <!--Subscription menu[3]-->
                <li class="nav-item has-treeview @if ($menu['3'])  menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['3']) active @endif ">
                        <i class="far fa-clock"></i>
                        <p>
                            Subscription
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Subscription Bills-->
                        <li class="nav-item">
                            <a href="{{ route('subscription_bills.index') }}"
                                class="nav-link @if ($link['3']['1']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Subscription Bills</p>
                            </a>
                        </li>
                        <!--/Subscription Bills-->

                        <!--Subscription Payments-->
                        <li class="nav-item">
                            <a href="{{ route('subscription_payments.index') }}"
                                class="nav-link @if ($link['3']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Subscription Payments</p>
                            </a>
                        </li>
                        <!--/Subscription Payments-->

                        <!--Download Report-->
                        <li class="nav-item">
                            <a href="{{ route('subscription-payment-report.create') }}"
                                class="nav-link @if ($link['3']['3']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Download Report</p>
                            </a>
                        </li>
                        <!--/Download Report-->

                    </ul>

                </li>
                <!--/Subscription menu[3]-->



                <!--SMS menus[4]-->
                <li class="nav-item has-treeview @if ($menu['4']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['4']) active @endif ">
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
                                class="nav-link @if ($link['4']['3']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Send SMS</p>
                            </a>
                        </li>
                        <!--/Send SMS-->


                        <!--SMS History-->
                        <li class="nav-item">
                            <a href="{{ route('sms_histories.index') }}"
                                class="nav-link @if ($link['4']['0']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>SMS History</p>
                            </a>
                        </li>
                        <!--/SMS History-->

                        <!--SMS Bills-->
                        <li class="nav-item">
                            <a href="{{ route('sms_bills.index') }}"
                                class="nav-link @if ($link['4']['1']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>SMS Bills</p>
                            </a>
                        </li>
                        <!--/SMS Bills-->

                        <!--SMS Payments-->
                        <li class="nav-item">
                            <a href="{{ route('sms_payments.index') }}"
                                class="nav-link @if ($link['4']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>SMS Payments</p>
                            </a>
                        </li>
                        <!--/SMS Payments-->

                    </ul>

                </li>
                <!--/SMS menu[4]-->



                <!--customer Online payments[5]-->
                <li class="nav-item has-treeview @if ($menu['5']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['5']) active @endif ">
                        <i class="fas fa-parking"></i>
                        <p>
                            Customer payments
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Customer payments-->
                        <li class="nav-item">
                            <a href="{{ route('customer_payments.index') }}"
                                class="nav-link @if ($link['5']['1']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Customer payments</p>
                            </a>
                        </li>
                        <!--/Customer payments-->

                        <!--Pending Transactions-->
                        <li class="nav-item">
                            <a href="{{ route('customers-pending-payments.index') }}"
                                class="nav-link @if ($link['5']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pending Payments</p>
                            </a>
                        </li>
                        <!--Pending Transactions-->

                    </ul>

                </li>
                <!--/Customer Online payments[5]-->



                <!--Expenses[6]-->
                <li class="nav-item has-treeview @if ($menu['6']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['6']) active @endif ">
                        <i class="far fa-minus-square"></i>
                        <p>
                            Expenses
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

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
                        <!--Expense Category-->

                        <!--Expense Report-->
                        <li class="nav-item">
                            <a href="{{ route('incomes-vs-expenses.index') }}"
                                class="nav-link @if ($link['6']['4']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Income Vs. Expense</p>
                            </a>
                        </li>
                        <!--Expense Category-->

                    </ul>

                </li>
                <!--/Expenses[6]-->



                <!--Security [7]-->
                <li class="nav-item has-treeview @if ($menu['7']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['7']) active @endif ">
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
                                class="nav-link @if ($link['7']['1']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                        <!--/Change Password-->

                        <!--Two Factor-->
                        <li class="nav-item">
                            <a href="{{ route('two-factor.show') }}"
                                class="nav-link @if ($link['7']['2']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Two Factor</p>
                            </a>
                        </li>
                        <!--/Two Factor-->

                        <!--Device Verification-->
                        <li class="nav-item">
                            <a href="{{ route('operators.device-identification.index', ['operator' => Auth::user()]) }}"
                                class="nav-link @if ($link['7']['3']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Device Verification</p>
                            </a>
                        </li>
                        <!--/Device Verification-->

                    </ul>

                </li>
                <!--/Settings[7]-->


                <!--Hotspot HTML menu[11] -->
                <li class="nav-item">
                    <a href="/storage/hotspot.zip" class="nav-link">
                        <i class="fas fa-download"></i>
                        <p>Hotspot HTML</p>
                    </a>
                </li>
                <!--/Hotspot HTML-->

                <!--Data Policy-->
                <li class="nav-item">
                    <a href="{{ route('data-policy.index') }}" class="nav-link @if ($menu['13']) active @endif ">
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