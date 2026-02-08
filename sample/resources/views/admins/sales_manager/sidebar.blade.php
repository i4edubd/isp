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
                ];

                $link = [
                '0' => ['0' => 0,'1' => 0,'2' => 0,],
                '1' => ['0' => 0,'1' => 0,'2' => 0,],
                '2' => ['0' => 0,'1' => 0,'2' => 0,],
                '3' => ['0' => 0,'1' => 0,'2' => 0,],
                '4' => ['0' => 0,'1' => 0,'2' => 0, '3' => 0, '4' => 0, '5' => 0],
                '5' => ['0' => 0,'1' => 0,'2' => 0,],
                '6' => ['0' => 0,'1' => 0,'2' => 0,],
                '7' => ['0' => 0,'1' => 0,'2' => 0,],
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

                <!--Operators [1] -->
                <li class="nav-item">
                    <a href="{{ route('self-registered-admins.index') }}"
                        class="nav-link @if ($menu['1']) active @endif ">
                        <i class="far fa-circle"></i>
                        <p>Self Registered Admins</p>
                    </a>
                </li>
                <!--/Operators-->

                <!--SMS menus[2]-->
                <li class="nav-item has-treeview @if ($menu['2']) menu-open @endif ">

                    <a href="#" class="nav-link @if ($menu['2']) active @endif ">
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
                                class="nav-link @if ($link['2']['1']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Send SMS</p>
                            </a>
                        </li>
                        <!--/Send SMS-->

                        <!--SMS History-->
                        <li class="nav-item">
                            <a href="{{ route('sms_histories.index') }}"
                                class="nav-link @if ($link['2']['2']) active @endif ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>SMS History</p>
                            </a>
                        </li>
                        <!--/SMS History-->

                    </ul>

                </li>
                <!--/SMS menu[2]-->

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

                    </ul>

                </li>
                <!--/Security[4]-->

            </ul>

        </nav>
        <!-- /sidebar-menu -->

    </div>
    <!-- /sidebar -->

</aside>
