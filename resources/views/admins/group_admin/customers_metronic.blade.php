@extends('layouts.group_admin_metronic')

@section('title', 'Customers')

@section('content')

<div class="card card-flush mb-6">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Customers List</h3>
            <a href="{{ route('customers.create') ?? '#' }}" class="inline-flex px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">+ Add Customer</a>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="card-body py-4 grid grid-cols-1 md:grid-cols-4 gap-4 border-b">
        <div class="text-center p-3 bg-emerald-50 rounded">
            <p class="text-xs text-slate-600">Online</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['online'] ?? 0 }}</p>
        </div>
        <div class="text-center p-3 bg-slate-50 rounded">
            <p class="text-xs text-slate-600">Offline</p>
            <p class="text-2xl font-bold text-slate-600">{{ $stats['offline'] ?? 0 }}</p>
        </div>
        <div class="text-center p-3 bg-yellow-50 rounded">
            <p class="text-xs text-slate-600">Suspended</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['suspended'] ?? 0 }}</p>
        </div>
        <div class="text-center p-3 bg-blue-50 rounded">
            <p class="text-xs text-slate-600">Total</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
        </div>
    </div>

    <div class="card-body py-4">
        {{-- Filters --}}
        <form method="GET" action="{{ route('customers.index') ?? '#' }}" class="mb-4 grid grid-cols-1 md:grid-cols-6 gap-2 items-end">
            <input type="text" name="username" placeholder="Username..." value="{{ request('username') }}" class="px-3 py-1.5 border rounded text-sm">
            <select name="status" class="px-3 py-1.5 border rounded text-sm">
                <option value="">Status...</option>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
                <option value="disabled">Disabled</option>
            </select>
            <select name="connection_type" class="px-3 py-1.5 border rounded text-sm">
                <option value="">Connection Type...</option>
                <option value="PPPoE">PPPoE</option>
                <option value="Hotspot">Hotspot</option>
            </select>
            <select name="billing_type" class="px-3 py-1.5 border rounded text-sm">
                <option value="">Billing Type...</option>
                <option value="Daily">Daily</option>
                <option value="Monthly">Monthly</option>
                <option value="Free">Free</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-slate-800 text-white rounded text-sm hover:bg-slate-900">Filter</button>
            <a href="{{ route('customers.index') }}" class="px-4 py-1.5 bg-slate-200 text-slate-800 rounded text-sm hover:bg-slate-300">Reset</a>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100">
                    <tr class="text-slate-700 font-medium">
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Username</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Package</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($customers ?? [] as $customer)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-blue-600">{{ $customer->customer_id }}</td>
                            <td class="px-4 py-3 font-medium">{{ $customer->username }}</td>
                            <td class="px-4 py-3">{{ $customer->name }}</td>
                            <td class="px-4 py-3">
                                @if($customer->status === 'active')
                                    <span class="inline-flex px-2 py-1 rounded text-xs bg-emerald-100 text-emerald-800">Active</span>
                                @elseif($customer->status === 'suspended')
                                    <span class="inline-flex px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Suspended</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-xs bg-slate-100 text-slate-800">{{ ucfirst($customer->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $customer->connection_type }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $customer->package_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('customers.edit', $customer->id) ?? '#' }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                                <span class="text-slate-300 mx-1">|</span>
                                <a href="{{ route('customers.show', $customer->id) ?? '#' }}" class="text-slate-600 hover:underline text-xs">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($customers) && $customers->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
