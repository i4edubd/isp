@php

    $links = [
        '0' => 0,
        '1' => 0,
        '2' => 0,
        '3' => 0,
        '4' => 0,
        '5' => 0,
        '6' => 0,
        '7' => 0,
        '8' => 0,
        '9' => 0,
        '10' => 0,
    ];

    if (isset($active_link)) {
        $links[$active_link] = 1;
    }

@endphp

<ul class="nav nav-pills card-header-pills">
    {{-- --}}
    <li class="nav-item">
        <a href="{{ route('customers.home') }}" class="nav-link @if ($links['9']) active @endif">
            <i class="fas fa-home"></i>
            {{ getLocaleString($operator->id, 'Home') }}
        </a>
    </li>
    {{-- --}}
    <li class="nav-item">
        <a href="{{ route('customers.card-recharge.create') }}"
            class="nav-link @if ($links['8']) active @endif">
            <i class="far fa-credit-card"></i>
            {{ getLocaleString($operator->id, 'Card Recharge') }}
        </a>
    </li>
    {{-- --}}
    <li class="nav-item">
        <a href="{{ route('customers.profile') }}" class="nav-link @if ($links['0']) active @endif">
            <i class="fas fa-user"></i>
            {{ getLocaleString($operator->id, 'Profile') }}
        </a>
    </li>
    {{-- --}}
    <li class="nav-item">
        <a href="{{ route('customers.packages') }}" class="nav-link @if ($links['1']) active @endif">
            <i class="fas fa-store"></i>
            {{ getLocaleString($operator->id, 'Buy Package') }}
        </a>
    </li>
    {{-- --}}
    <li class="nav-item">
        <a href="{{ route('customers.radaccts') }}" class="nav-link @if ($links['2']) active @endif">
            <i class="fas fa-history"></i>
            {{ getLocaleString($operator->id, 'Internet History') }}
        </a>
    </li>
    {{-- --}}
    <li class="nav-item">
        <a href="{{ route('customers.graph') }}" class="nav-link @if ($links['7']) active @endif">
            <i class="fas fa-chart-bar"></i>
            {{ getLocaleString($operator->id, 'Bandwidth Graph') }}
        </a>
    </li>
    {{-- --}}
    <a href="{{ route('customers.card-stores') }}" class="nav-link @if ($links['3']) active @endif">
        <i class="fas fa-store"></i>
        {{ getLocaleString($operator->id, 'Card Stores') }}
    </a>
    {{-- --}}
    <li class="nav-item">
        <a href="{{ route('customers.bills') }}" class="nav-link @if ($links['4']) active @endif">
            <i class="fas fa-file-invoice-dollar"></i>
            {{ getLocaleString($operator->id, 'Bills') }}
        </a>
    </li>
    {{-- --}}
    <li class="nav-item">
        <a href="{{ route('customers.payments') }}" class="nav-link @if ($links['5']) active @endif">
            <i class="fas fa-history"></i>
            {{ getLocaleString($operator->id, 'Payment History') }}
        </a>
    </li>
    {{-- --}}
    <li class="nav-item">
        <a href="{{ route('complaints-customer-interface.index') }}"
            class="nav-link @if ($links['6']) active @endif">
            <i class="fas fa-mail-bulk"></i>
            {{ getLocaleString($operator->id, 'Complaints') }}
        </a>
    </li>
    {{-- --}}

</ul>
