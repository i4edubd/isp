@extends('layouts.group_admin_metronic')

@section('title', 'Packages')

@section('content')

<div class="card card-flush mb-6">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Packages List</h3>
            <a href="{{ route('packages.create') ?? '#' }}" class="inline-flex px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">+ Add Package</a>
        </div>
    </div>

    <div class="card-body py-4">
        {{-- Filters --}}
        <form method="GET" action="{{ route('packages.index') ?? '#' }}" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-2">
            <input type="text" name="name" placeholder="Package name..." value="{{ request('name') }}" class="px-3 py-1.5 border rounded text-sm">
            <select name="connection_type" class="px-3 py-1.5 border rounded text-sm">
                <option value="">Connection Type...</option>
                <option value="PPPoE">PPPoE</option>
                <option value="Hotspot">Hotspot</option>
            </select>
            <select name="billing_type" class="px-3 py-1.5 border rounded text-sm">
                <option value="">Billing Type...</option>
                <option value="Daily">Daily</option>
                <option value="Monthly">Monthly</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-slate-800 text-white rounded text-sm hover:bg-slate-900">Filter</button>
            <a href="{{ route('packages.index') }}" class="px-4 py-1.5 bg-slate-200 text-slate-800 rounded text-sm hover:bg-slate-300">Reset</a>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100">
                    <tr class="text-slate-700 font-medium">
                        <th class="px-4 py-3 text-left">Package Name</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Connection</th>
                        <th class="px-4 py-3 text-left">Speed Limit</th>
                        <th class="px-4 py-3 text-left">Volume Limit</th>
                        <th class="px-4 py-3 text-left">Price</th>
                        <th class="px-4 py-3 text-left">Validity</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($packages ?? [] as $package)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $package->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                    {{ ucfirst($package->master_package->visibility ?? 'private') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $package->master_package->connection_type }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @if($package->master_package->rate_limit)
                                    {{ $package->master_package->rate_limit }} {{ $package->master_package->readable_rate_unit }}
                                @else
                                    Unlimited
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @if($package->master_package->volume_limit)
                                    {{ $package->master_package->volume_limit }} {{ $package->volume_unit }}
                                @else
                                    Unlimited
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">{{ config('consumer.currency_symbol') }} {{ $package->price }}</td>
                            <td class="px-4 py-3 text-xs">{{ $package->master_package->validity }} days</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('packages.edit', $package->id) ?? '#' }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                                <span class="text-slate-300 mx-1">|</span>
                                <a href="#" class="text-red-600 hover:underline text-xs">Delete</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">No packages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($packages) && $packages->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $packages->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
