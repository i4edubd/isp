<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ISP Billing') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-800">

    <div class="kt-header kt-header-fixed w-full bg-white shadow-sm" data-kt-sticky="true" data-kt-sticky-offset="0">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center">
            <a href="/" class="font-semibold text-lg">ISP Billing</a>
            <div class="ml-6 text-sm text-slate-500">@yield('company')</div>
            <div class="ml-auto">@yield('topNavbar')</div>
        </div>
    </div>

    <div class="flex pt-16">
        <aside class="w-72 bg-primary-600 text-white min-h-screen p-4">
            <div class="mb-6 text-white font-semibold">Menu</div>
            <nav class="space-y-1">
                {{-- Example menu; pages should render their own menu where needed --}}
                <a href="/" class="menu-link block px-3 py-2 rounded hover:bg-white/10">Dashboard</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div class="max-w-7xl mx-auto">
                <div class="mb-4 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold">@yield('title')</h1>
                        <div class="text-sm text-slate-500">@yield('contentTitle')</div>
                    </div>
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
