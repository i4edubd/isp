<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Group Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-800">

    <div class="flex h-screen">
        {{-- Sidebar --}}
        <aside class="w-72 bg-slate-900 text-white shadow-lg overflow-y-auto">
            <div class="sticky top-0 p-6 border-b border-slate-700 bg-slate-800">
                <h2 class="text-lg font-bold">Group Admin</h2>
                <p class="text-xs text-slate-400 mt-1">{{ $operator->name ?? 'ISP Billing' }}</p>
            </div>
            <nav class="p-4 space-y-1">
                <a href="/" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm font-medium">Dashboard</a>

                <div class="mt-4 mb-2">
                    <span class="px-4 text-xs font-semibold text-slate-400 uppercase">Customers</span>
                </div>
                <a href="{{ route('customers.index') ?? '#' }}" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Customers</a>
                <a href="{{ route('online_customers.index') ?? '#' }}" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Online Customers</a>
                <a href="{{ route('offline_customers.index') ?? '#' }}" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Offline Customers</a>

                <div class="mt-4 mb-2">
                    <span class="px-4 text-xs font-semibold text-slate-400 uppercase">Management</span>
                </div>
                <a href="{{ route('operators.index') ?? '#' }}" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Operators</a>
                <a href="{{ route('packages.index') ?? '#' }}" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Packages</a>
                <a href="{{ route('routers.index') ?? '#' }}" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Routers</a>
                <a href="{{ route('billing-profiles.index') ?? '#' }}" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Billing Profiles</a>

                <div class="mt-4 mb-2">
                    <span class="px-4 text-xs font-semibold text-slate-400 uppercase">Finance</span>
                </div>
                <a href="#" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Accounts</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Reports</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">SMS Billing</a>

                <div class="mt-4 mb-2">
                    <span class="px-4 text-xs font-semibold text-slate-400 uppercase">Settings</span>
                </div>
                <a href="#" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Custom Fields</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Change Password</a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            {{-- Header --}}
            <header class="bg-white shadow-sm h-16 flex items-center px-6 border-b">
                <div class="flex-1">
                    <h1 class="text-2xl font-semibold">@yield('title')</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-600">{{ Auth::user()->name ?? 'Group Admin' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-blue-600 hover:underline">Logout</button>
                    </form>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 overflow-auto p-6">
                <div class="max-w-7xl">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
