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
                '20' => 0,
                ];

                $link = [
                '0' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '1' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '2' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '3' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '4' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0, '6' => 0],
                '5' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '6' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '7' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '8' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '9' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                '20' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0 , '4' => 0, '5' => 0],
                ];

                if(isset($active_menu)){
                $menu[$active_menu] = 1;
                }

                if(isset($active_link)){
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
                    <a href="{{ route('admin.dashboard') }}" class="nav-link @if ($menu['0']) active @endif ">
                        <i class="fas fa-palette"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!--/Dashboard-->

                {{-- Customers [1] --}}
                @if (Auth::user()->permissions->contains('view-customer-details'))
                <li class="nav-item">
                    <a href="{{ route('customers.index') }}" class="nav-link @if ($menu['1']) active @endif ">
                        <i class="fas fa-user"></i>
                        <p>All Customers</p>
                    </a>
                </li>
                @endif
                {{-- Customers [1] --}}

                {{-- Online Customers [5] --}}
                @if (Auth::user()->permissions->contains('view-online-customers'))
                <li class="nav-item">
                    <a href="{{ route('online_customers.index') }}" class="nav-link @if ($menu['5']) active @endif ">
                        <i class="fas fa-user"></i>
                        <p>Online Customers</p>
                    </a>
                </li>
                @endif
                {{-- Online Customers [5] --}}


                {{-- Offline Customers [6] --}}
                @if (Auth::user()->permissions->contains('view-offline-customers'))
                <li class="nav-item">
                    <a href="{{ route('offline_customers.index') }}" class="nav-link @if ($menu['6']) active @endif ">
                        <i class="fas fa-user"></i>
                        <p>Offline Customers</p>
                    </a>
                </li>
                @endif
                {{-- Offline Customers [6] --}}

                {{-- New Customer [2] --}}
                @if (Auth::user()->permissions->contains('create-customer'))
                <li class="nav-item">
                    <a href="{{ route('temp_customers.create') }}" class="nav-link @if ($menu['2']) active @endif ">
                        <i class="fas fa-plus"></i>
                        <p>New Customer</p>
                    </a>
                </li>
                @endif
                {{-- New Customer [2] --}}

                {{-- Bills [3] --}}
                @if (Auth::user()->permissions->contains('print-invoice'))
                <li class="nav-item">
                    <a href="{{ route('customer_bills.index') }}" class="nav-link @if ($menu['3']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Bills</p>
                    </a>
                </li>
                @endif
                {{-- Bills [3] --}}

                {{-- Payments [8] --}}
                @if (Auth::user()->permissions->contains('view-customer-payments'))
                <li class="nav-item">
                    <a href="{{ route('customer_payments.index') }}" class="nav-link @if ($menu['8']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Payments</p>
                    </a>
                </li>
                @endif
                {{-- Payments [8] --}}

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

                    </ul>

                </li>
                <!--/complaint-management[20]-->

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
                                class="nav-link @if ($link['4']['4']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Device Verification</p>
                            </a>
                        </li>
                        <!--/Device Verification-->

                        <!--activity_logs-->
                        <li class="nav-item">
                            <a href="{{ route('activity_logs.index') }}"
                                class="nav-link @if ($link['4']['3']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Activity Logs</p>
                            </a>
                        </li>
                        <!--/activity_logs-->

                        <!--Authentication logs-->
                        <li class="nav-item">
                            <a href="{{ route('authentication_log.index') }}"
                                class="nav-link @if ($link['4']['5']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Authentication logs</p>
                            </a>
                        </li>
                        <!--/Authentication logs-->

                    </ul>

                </li>
                <!--/Security[4]-->

                {{-- Expenses [7] --}}
                @if (Auth::user()->permissions->contains('expense-management'))
                <li class="nav-item">
                    <a href="{{ route('expenses.index') }}" class="nav-link @if ($menu['7']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Expenses</p>
                    </a>
                </li>
                @endif
                {{-- Expenses [7] --}}

            </ul>

        </nav>
        <!-- /sidebar-menu -->
         <!-- Include global search result modal -->
        @include('laraview.layouts.global_search_result_modal')
        <!-- /Include global search result modal -->

    </div>
    <!-- /sidebar -->

</aside>
