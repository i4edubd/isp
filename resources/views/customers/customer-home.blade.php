@extends('layouts.metronic_demo1')

@section('title')
    Home
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('contentTitle')
    @include('customers.logout-nav')
@endsection

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="card card-flush shadow-sm text-center p-4">
        <div class="card-body">
            <a class="inline-flex flex-col items-center gap-2" href="{{ route('customers.card-recharge.create') }}">
                <span class="svg-icon svg-icon-3 text-primary"><!-- icon --></span>
                <h6 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Card Recharge') }}</h6>
            </a>
        </div>
    </div>

    <div class="card card-flush shadow-sm text-center p-4">
        <div class="card-body">
            <a class="inline-flex flex-col items-center gap-2" href="{{ route('customers.packages') }}">
                <span class="svg-icon svg-icon-3 text-emerald-600"><!-- icon --></span>
                <h6 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Buy Package') }}</h6>
            </a>
        </div>
    </div>

    <div class="card card-flush shadow-sm text-center p-4">
        <div class="card-body">
            <a class="inline-flex flex-col items-center gap-2" href="{{ route('customers.card-stores') }}">
                <span class="svg-icon svg-icon-3 text-yellow-500"><!-- icon --></span>
                <h6 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Card Stores') }}</h6>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
    <div class="card card-flush shadow-sm text-center p-4">
        <div class="card-body">
            <a class="inline-flex flex-col items-center gap-2" href="{{ route('customers.profile') }}">
                <span class="svg-icon svg-icon-3 text-sky-500"><!--icon--></span>
                <h6 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Profile') }}</h6>
            </a>
        </div>
    </div>

    <div class="card card-flush shadow-sm text-center p-4">
        <div class="card-body">
            <a class="inline-flex flex-col items-center gap-2" href="{{ route('customers.radaccts') }}">
                <span class="svg-icon svg-icon-3 text-indigo-600"><!--icon--></span>
                <h6 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Internet History') }}</h6>
            </a>
        </div>
    </div>

    <div class="card card-flush shadow-sm text-center p-4">
        <div class="card-body">
            <a class="inline-flex flex-col items-center gap-2" href="{{ route('customers.graph') }}">
                <span class="svg-icon svg-icon-3 text-rose-500"><!--icon--></span>
                <h6 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Bandwidth Graph') }}</h6>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
    <div class="card card-flush shadow-sm text-center p-4">
        <div class="card-body">
            <a class="inline-flex flex-col items-center gap-2" href="{{ route('customers.bills') }}">
                <span class="svg-icon svg-icon-3 text-violet-600"><!--icon--></span>
                <h6 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Bills') }}</h6>
            </a>
        </div>
    </div>

    <div class="card card-flush shadow-sm text-center p-4">
        <div class="card-body">
            <a class="inline-flex flex-col items-center gap-2" href="{{ route('customers.payments') }}">
                <span class="svg-icon svg-icon-3 text-emerald-600"><!--icon--></span>
                <h6 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Payment History') }}</h6>
            </a>
        </div>
    </div>

    <div class="card card-flush shadow-sm text-center p-4">
        <div class="card-body">
            <a class="inline-flex flex-col items-center gap-2" href="{{ route('complaints-customer-interface.index') }}">
                <span class="svg-icon svg-icon-3 text-cyan-600"><!--icon--></span>
                <h6 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Complaints') }}</h6>
            </a>
        </div>
    </div>
</div>

@endsection
