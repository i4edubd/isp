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

<div class="card card-flush mb-4">
    <div class="card-header">
        <div class="card-title">
            <h3 class="text-sm font-medium">{{ getLocaleString($operator->id, 'Navigation') }}</h3>
        </div>
    </div>
    <div class="card-body py-3">
        <ul class="menu-menu menu-column">
            <li class="menu-item">
                <a href="{{ route('customers.home') }}" class="menu-link @if($links['9']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--home svg--></span>
                    {{ getLocaleString($operator->id, 'Home') }}
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('customers.card-recharge.create') }}" class="menu-link @if($links['8']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--card svg--></span>
                    {{ getLocaleString($operator->id, 'Card Recharge') }}
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('customers.profile') }}" class="menu-link @if($links['0']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--user svg--></span>
                    {{ getLocaleString($operator->id, 'Profile') }}
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('customers.packages') }}" class="menu-link @if($links['1']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--store svg--></span>
                    {{ getLocaleString($operator->id, 'Buy Package') }}
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('customers.radaccts') }}" class="menu-link @if($links['2']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--history svg--></span>
                    {{ getLocaleString($operator->id, 'Internet History') }}
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('customers.graph') }}" class="menu-link @if($links['7']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--chart svg--></span>
                    {{ getLocaleString($operator->id, 'Bandwidth Graph') }}
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('customers.card-stores') }}" class="menu-link @if($links['3']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--store svg--></span>
                    {{ getLocaleString($operator->id, 'Card Stores') }}
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('customers.bills') }}" class="menu-link @if($links['4']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--invoice svg--></span>
                    {{ getLocaleString($operator->id, 'Bills') }}
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('customers.payments') }}" class="menu-link @if($links['5']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--payments svg--></span>
                    {{ getLocaleString($operator->id, 'Payment History') }}
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('complaints-customer-interface.index') }}" class="menu-link @if($links['6']) active @endif">
                    <span class="svg-icon svg-icon-2 me-2"><!--complaint svg--></span>
                    {{ getLocaleString($operator->id, 'Complaints') }}
                </a>
            </li>
        </ul>
    </div>
</div>
