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
                    ];

                    $link = [
                        '0' => ['0' => 0, '1' => 0, '2' => 0],
                        '1' => ['0' => 0, '1' => 0, '2' => 0],
                        '2' => ['0' => 0, '1' => 0, '2' => 0],
                        '3' => ['0' => 0, '1' => 0, '2' => 0],
                        '4' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0],
                        '5' => ['0' => 0, '1' => 0, '2' => 0],
                        '6' => ['0' => 0, '1' => 0, '2' => 0],
                        '7' => ['0' => 0, '1' => 0, '2' => 0],
                        '8' => ['0' => 0, '1' => 0, '2' => 0],
                        '9' => ['0' => 0, '1' => 0, '2' => 0],
                        '10' => ['0' => 0, '1' => 0, '2' => 0],
                        '12' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0],
                        '13' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
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
                    <a href="{{ route('dashboard') }}"
                        class="nav-link @if ($menu['0']) active @endif ">
                        <i class="fas fa-palette"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!--/Dashboard-->

                <!--Payment Gateways menu[1] -->
                <li class="nav-item">
                    <a href="{{ route('payment_gateways.index') }}"
                        class="nav-link @if ($menu['1']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Payment Gateways</p>
                    </a>
                </li>
                <!--/Payment Gateways-->

                <!--SMS Gateways menu[2] -->
                <li class="nav-item">
                    <a href="{{ route('sms_gateways.index') }}"
                        class="nav-link @if ($menu['2']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>SMS Gateways</p>
                    </a>
                </li>
                <!--/SMS Gateways-->

                <!--Notice Broadcast[8] -->
                <li class="nav-item">
                    <a href="{{ route('developer-notice-broadcast.create') }}"
                        class="nav-link @if ($menu['8']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Notice Broadcast</p>
                    </a>
                </li>
                <!--/Notice Broadcast -->

                <!--Operators [3] -->
                <li class="nav-item">
                    <a href="{{ route('operators-delete.index') }}"
                        class="nav-link @if ($menu['3']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Operators</p>
                    </a>
                </li>
                <!--/Operators-->

                <!--Device Monitoring [14] -->
                <li class="nav-item">
                    <a href="{{ route('device-monitors.index') }}"
                        class="nav-link @if ($menu['14']) active @endif ">
                        <i class="fas fa-network-wired"></i>
                        <p>Device Monitoring</p>
                    </a>
                </li>
                <!--/Device Monitoring-->

                <!--Accounts menus[13]-->
                <li class="nav-item has-treeview @if ($menu['13']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['13']) active @endif ">
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
                                class="nav-link @if ($link['13']['1']) active @endif ">
                                <i class=""></i>
                                <i class="far fa-circle nav-icon"></i>
                                <p>Accounts Receivable</p>
                            </a>
                        </li>
                        <!--/Accounts Receivable-->

                        <!--Accounts Payable-->
                        <li class="nav-item">
                            <a href="{{ route('accounts.payable') }}"
                                class="nav-link @if ($link['13']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Accounts Payable</p>
                            </a>
                        </li>
                        <!--/Accounts Payable-->

                        <!--Pending Transactions-->
                        <li class="nav-item">
                            <a href="{{ route('pending_transactions.index') }}"
                                class="nav-link @if ($link['13']['3']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pending Transactions</p>
                            </a>
                        </li>
                        <!--/Pending Transactions-->

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
                                class="nav-link @if ($link['13']['4']) active @endif ">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Monthly Report</p>
                            </a>
                        </li>
                        <!--/Monthly Report-->

                    </ul>

                </li>
                <!--/Accounts menu[13]-->

                <!--Security [4]-->
                <li class="nav-item has-treeview @if ($menu['4']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['4']) active @endif ">
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
                                class="nav-link @if ($link['4']['1']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                        <!--/Change Password-->

                        <!--Two Factor-->
                        <li class="nav-item">
                            <a href="{{ route('two-factor.show') }}"
                                class="nav-link @if ($link['4']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Two Factor</p>
                            </a>
                        </li>
                        <!--/Two Factor-->

                        <!--Device Verification-->
                        <li class="nav-item">
                            <a href="{{ route('operators.device-identification.index', ['operator' => Auth::user()]) }}"
                                class="nav-link @if ($link['4']['3']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Device Verification</p>
                            </a>
                        </li>
                        <!--/Device Verification-->

                    </ul>

                </li>
                <!--/Security[4]-->

                <!--Foreign Routers[5] -->
                <li class="nav-item">
                    <a href="{{ route('foreign-routers.create') }}"
                        class="nav-link @if ($menu['5']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Foreign Routers</p>
                    </a>
                </li>
                <!--/Foreign Routers-->

                <!--Discounts[6] -->
                <li class="nav-item">
                    <a href="{{ route('subscription_discounts.index') }}"
                        class="nav-link @if ($menu['6']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Discounts</p>
                    </a>
                </li>
                <!--/Discounts-->

                <!--Max[9] -->
                <li class="nav-item">
                    <a href="{{ route('max_subscription_payments.index') }}"
                        class="nav-link @if ($menu['9']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Max Subscription Fee</p>
                    </a>
                </li>
                <!--/Max-->

                <!--Minimum SMS Bill[7] -->
                <li class="nav-item">
                    <a href="{{ route('minimum_sms_bills.index') }}"
                        class="nav-link @if ($menu['7']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Minimum SMS Bill</p>
                    </a>
                </li>
                <!--/Minimum SMS Bill-->

                <!--VPN [4]-->
                <li class="nav-item has-treeview @if ($menu['12']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['12']) active @endif ">
                        <i class="fas fa-key"></i>
                        <p>
                            VPN
                            <i class="fas fa-caret-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <!--Change Password-->
                        <li class="nav-item">
                            <a href="{{ route('routers.index') }}"
                                class="nav-link @if ($link['12']['1']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>VPN Router</p>
                            </a>
                        </li>
                        <!--/Change Password-->

                        <!--VPN Pools-->
                        <li class="nav-item">
                            <a href="{{ route('vpn-pools.index') }}"
                                class="nav-link @if ($link['12']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>VPN Pools</p>
                            </a>
                        </li>
                        <!--/VPN Pools-->

                        <!--VPN Accounts-->
                        <li class="nav-item">
                            <a href="{{ route('vpn_accounts.index') }}"
                                class="nav-link @if ($link['12']['3']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>VPN Accounts</p>
                            </a>
                        </li>
                        <!--/VPN Accounts-->

                    </ul>

                </li>
                <!--/VPN[12]-->

                <!--Logs -->
                <li class="nav-item">
                    <a href="{{ route('LaravelLogs') }}" class="nav-link">
                        <i class="far fa-circle"></i>
                        <p>Logs</p>
                    </a>
                </li>
                <!--/Logs-->

                <!--Failed Logins -->
                <li class="nav-item">
                    <a href="{{ route('failed-logins') }}" class="nav-link">
                        <i class="far fa-circle"></i>
                        <p>Failed Logins</p>
                    </a>
                </li>
                <!--/Failed Logins-->

                <!--Questions [3] -->
                <li class="nav-item">
                    <a href="{{ route('questions.index') }}"
                        class="nav-link @if ($menu['11']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Questions & Answers</p>
                    </a>
                </li>
                <!--/Questions-->

            </ul>

        </nav>
        <!-- /sidebar-menu -->

    </div>
    <!-- /sidebar -->

</aside>
