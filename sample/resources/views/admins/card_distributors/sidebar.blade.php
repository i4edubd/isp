<aside class="main-sidebar {{ config('consumer.app_skin') }} elevation-4">

    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ Auth::guard('card')->user()->name }}</span>
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
                    ];

                    $link = [
                        '0' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '1' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '2' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '3' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '4' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
                        '5' => ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0],
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
                    <a href="{{ route('card.dashboard') }}"
                        class="nav-link @if ($menu['0']) active @endif ">
                        <i class="fas fa-palette"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!--/Dashboard-->

                {{-- Top-up [1] --}}
                <li class="nav-item">
                    <a href="{{ route('card.search-customer.create') }}"
                        class="nav-link @if ($menu['1']) active @endif ">
                        <i class="fas fa-battery-quarter"></i>
                        <p>Top-up</p>
                    </a>
                </li>
                {{-- Top-up [1] --}}

                {{-- Recharge History [2] --}}
                <li class="nav-item">
                    <a href="{{ route('card.recharge-history') }}"
                        class="nav-link @if ($menu['2']) active @endif ">
                        <i class="fas fa-money-check"></i>
                        <p>Recharge History</p>
                    </a>
                </li>
                {{-- Recharge History [2] --}}

                {{-- Payment History [3] --}}
                <li class="nav-item">
                    <a href="{{ route('card.payment-history') }}"
                        class="nav-link @if ($menu['3']) active @endif ">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <p>Payment History</p>
                    </a>
                </li>
                {{-- Payment History [3] --}}

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
                            <a href="{{ route('card.change-password.create') }}" class="nav-link @if ($link['4']['1']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                        <!--/Change Password-->

                    </ul>

                </li>
                <!--/Security[4]-->

            </ul>

        </nav>
        <!-- /sidebar-menu -->

    </div>
    <!-- /sidebar -->

</aside>
