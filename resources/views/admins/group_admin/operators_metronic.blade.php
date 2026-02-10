@extends('layouts.group_admin_metronic')

@section('title', 'Operators')

@section('content')

<div class="card card-flush mb-6">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Operators List</h3>
            <a href="{{ route('operators.create') ?? '#' }}" class="inline-flex px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">+ Add Operator</a>
        </div>
    </div>

    <div class="card-body py-4">
        {{-- Filters --}}
        <form method="GET" action="{{ route('operators.index') ?? '#' }}" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-2">
            <input type="text" name="name" placeholder="Name..." value="{{ request('name') }}" class="px-3 py-1.5 border rounded text-sm">
            <select name="role" class="px-3 py-1.5 border rounded text-sm">
                <option value="">Role...</option>
                <option value="operator">Operator</option>
                <option value="sub_operator">Sub Operator</option>
                <option value="reseller">Reseller</option>
            </select>
            <select name="status" class="px-3 py-1.5 border rounded text-sm">
                <option value="">Status...</option>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-slate-800 text-white rounded text-sm hover:bg-slate-900">Filter</button>
            <a href="{{ route('operators.index') }}" class="px-4 py-1.5 bg-slate-200 text-slate-800 rounded text-sm hover:bg-slate-300">Reset</a>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100">
                    <tr class="text-slate-700 font-medium">
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Company</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($operators ?? [] as $operator)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $operator->id }}</td>
                            <td class="px-4 py-3">{{ $operator->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                    {{ ucfirst($operator->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $operator->company }}</td>
                            <td class="px-4 py-3">
                                @if($operator->status === 'active')
                                    <span class="inline-flex px-2 py-1 rounded text-xs bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-xs bg-rose-100 text-rose-800">{{ ucfirst($operator->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('operators.edit', $operator->id) ?? '#' }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                                <span class="text-slate-300 mx-1">|</span>
                                <a href="{{ route('operators.show', $operator->id) ?? '#' }}" class="text-slate-600 hover:underline text-xs">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">No operators found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($operators) && $operators->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $operators->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
