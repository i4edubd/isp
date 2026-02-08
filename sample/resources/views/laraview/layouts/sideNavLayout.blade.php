<!DOCTYPE html>
<html>

<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-T66VMR5', {
            cookie_flags: 'SameSite=None;Secure'
        });
    </script>
    <!-- End Google Tag Manager -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title')</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('laraview.layouts.vendorCss')
    @include('laraview.css.commonCss')
    @include('laraview.css.appCss')
    @yield('pageCss')

</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <!-- Site wrapper -->
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
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
            

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">

                @if (Auth::user())

                    @if (isMobileDevice() == false)
                        @if (getSoftwareSupportNumber(Auth::user()->id))
                            <li class="nav-item mt-2">
                                <a href="{{ route('helpline') }}" data-toggle="tooltip" data-placement="top"
                                    title="Help and Support">
                                    <i class="fas fa-question-circle"></i>
                                </a>
                            </li>
                        @endif
                    @endif

                <!-- <li class="nav-item d-none d-sm-inline-block"> -->
                <li class="nav-item inline-block">
                @if (collect(['operator', 'sub_operator'])->contains(Auth::user()->role))
                    @php
                $collection = getAccountInfo(Auth::user());
                @endphp
                <a href="{{ $collection->get('url') }}" class="nav-link">
                    {{ $collection->get('balance') }} |
                    @if (strlen($collection->get('msg')))
                        <span class="btn btn-outline-info btn-sm"> {{ $collection->get('msg') }} </span>
                @endif
                </a>
                @endif
            </li>
                
            

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ explode(' ', Auth::user()->name)[1] }} <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>

                @endif

            </ul>

        </nav>
        <!-- /navbar -->

        <!--Active Link-->
        @yield('activeLink')
        <!--/Active Link-->

        <!-- Main Sidebar Container -->
        @yield('sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            @yield('contentTitle')
                        </div>
                        <div class="col-sm-6">
                            @yield('breadcrumb')
                        </div>
                    </div>
                </div>
                <!-- /container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                @include('laraview.layouts.wait')
                @include('laraview.layouts.global_search_result_modal')
                @yield('content')
            </section>
            <!-- /content -->

        </div>
        <!-- /content-wrapper -->

        @include('laraview.layouts.footer')
        <!-- Control Sidebar -->

        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /control-sidebar -->

    </div>
    <!-- /wrapper -->

    @include('laraview.layouts.vendorJs')
    @include('laraview.js.commonJs')
    @include('laraview.js.appJs')
    @yield('pageJs')
    @stack('scripts')

</body>

</html>
