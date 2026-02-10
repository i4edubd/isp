@extends('layouts.group_admin_metronic')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    {{-- Online Customers --}}
    <div class="card card-flush shadow-sm cursor-pointer hover:shadow-md transition" onclick="window.location.href='{{ route('online_customers.index') }}'">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 font-medium">Online Customers</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['online'] ?? 0 }}</p>
                </div>
                <div class="text-4xl text-emerald-200">🌐</div>
            </div>
        </div>
    </div>

    {{-- Offline Customers --}}
    <div class="card card-flush shadow-sm cursor-pointer hover:shadow-md transition" onclick="window.location.href='{{ route('offline_customers.index') }}'">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 font-medium">Offline Customers</p>
                    <p class="text-2xl font-bold text-slate-600 mt-1">{{ $stats['offline'] ?? 0 }}</p>
                </div>
                <div class="text-4xl text-slate-300">⊘</div>
            </div>
        </div>
    </div>

    {{-- Active Operators --}}
    <div class="card card-flush shadow-sm cursor-pointer hover:shadow-md transition" onclick="window.location.href='{{ route('operators.index') }}'">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 font-medium">Active Operators</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['operators'] ?? 0 }}</p>
                </div>
                <div class="text-4xl text-blue-200">👥</div>
            </div>
        </div>
    </div>

    {{-- Total Revenue --}}
    <div class="card card-flush shadow-sm cursor-pointer hover:shadow-md transition">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ config('consumer.currency_symbol') }} {{ $stats['revenue'] ?? '0.00' }}</p>
                </div>
                <div class="text-4xl text-green-200">💰</div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="card card-flush">
        <div class="card-body">
            <h5 class="text-sm font-semibold mb-3">Quick Actions</h5>
            <div class="space-y-2">
                <a href="{{ route('customers.create') ?? '#' }}" class="block px-3 py-2 bg-blue-50 text-blue-700 rounded text-sm hover:bg-blue-100">+ Add Customer</a>
                <a href="{{ route('operators.create') ?? '#' }}" class="block px-3 py-2 bg-purple-50 text-purple-700 rounded text-sm hover:bg-purple-100">+ Add Operator</a>
                <a href="{{ route('packages.create') ?? '#' }}" class="block px-3 py-2 bg-emerald-50 text-emerald-700 rounded text-sm hover:bg-emerald-100">+ Add Package</a>
            </div>
        </div>
    </div>

    <div class="card card-flush">
        <div class="card-body">
            <h5 class="text-sm font-semibold mb-3">Recent Activity</h5>
            <ul class="space-y-2 text-xs text-slate-600">
                <li class="flex justify-between">
                    <span>New registration</span>
                    <span class="text-slate-400">5 min ago</span>
                </li>
                <li class="flex justify-between">
                    <span>Payment received</span>
                    <span class="text-slate-400">15 min ago</span>
                </li>
                <li class="flex justify-between">
                    <span>Router online</span>
                    <span class="text-slate-400">1 hour ago</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="card card-flush">
        <div class="card-body">
            <h5 class="text-sm font-semibold mb-3">System Status</h5>
            <ul class="space-y-2 text-xs">
                <li class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>All Routers Online</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Database Connected</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                    <span>Disk Usage: 75%</span>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection
