<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-800">

    <div class="flex h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 bg-slate-900 text-white shadow-lg">
            <div class="p-6 border-b border-slate-700">
                <h2 class="text-lg font-bold">Admin Panel</h2>
            </div>
            <nav class="p-4 space-y-2">
                <a href="/" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Dashboard</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Customers</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Packages</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Billing</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-slate-700 text-sm">Reports</a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            {{-- Header --}}
            <header class="bg-white shadow-sm h-16 flex items-center px-6 border-b">
                <div class="flex-1">
                    <h1 class="text-xl font-semibold">@yield('title')</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-600">{{ Auth::user()->name ?? 'User' }}</span>
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
