@extends ('laraview.layouts.topNavLayout')

@section('title')
    Profile
@endsection

@section('pageCss')
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('topNavbar')
@endsection

@section('contentTitle')
    @include('customers.logout-nav')
@endsection

@section('content')

    <div class="card">

        {{-- Navigation bar --}}
        <div class="card-header">
            @php
                $active_link = '0';
            @endphp
            @include('customers.nav-links')
        </div>
        {{-- Navigation bar --}}

        <div class="card-body">
            @if ($customer->status == 'active')
                <h5 class="card-title text-success">{{ getLocaleString($operator->id, 'Status') }} : {{ $customer->status }}
                </h5>
            @else
                <h5 class="card-title text-danger">{{ getLocaleString($operator->id, 'Status') }} : {{ $customer->status }}
                </h5>
            @endif
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                {{ getLocaleString($operator->id, 'Connection Type') }} :
                {{ $customer->connection_type }}
            </li>
            <li class="list-group-item">
                {{ getLocaleString($operator->id, 'Name') }} :
                {{ $customer->name }}
            </li>
            <li class="list-group-item">
                {{ getLocaleString($operator->id, 'Mobile') }} :
                {{ $customer->mobile }}
            </li>
            <li class="list-group-item">
                {{ getLocaleString($operator->id, 'Username') }} :
                {{ $customer->username }}
            </li>
            <li class="list-group-item">
                {{ getLocaleString($operator->id, 'Password') }} :
                {{ $customer->password }}
            </li>
            <li class="list-group-item">
                {{ getLocaleString($operator->id, 'Active Package') }} :
                {{ $customer->package_name }}
            </li>
            <li class="list-group-item">
                {{ getLocaleString($operator->id, 'Package Updated At') }} :
                {{ $customer->last_recharge_time }}
            </li>
            <li class="list-group-item">
                {{ getLocaleString($operator->id, 'Valid Untill') }} :
                {{ $customer->package_expired_at }}
            </li>

            {{-- Only for Hotspot Customer --}}
            @if ($customer->connection_type == 'Hotspot')
                @if ($customer->rate_limit)
                    <li class="list-group-item">
                        {{ getLocaleString($operator->id, 'Speed Limit') }} :
                        {{ $customer->rate_limit }} Mbps
                    </li>
                @else
                    <li class="list-group-item">
                        {{ getLocaleString($operator->id, 'Speed Limit') }} :
                        No Limit
                    </li>
                @endif

                @if ($customer->total_octet_limit)
                    <li class="list-group-item">
                        {{ getLocaleString($operator->id, 'Volume Limit') }} :
                        {{ $customer->total_octet_limit / 1000000 }} MB
                    </li>
                @else
                    <li class="list-group-item">
                        {{ getLocaleString($operator->id, 'Volume Limit') }} :
                        Unlimited MB
                    </li>
                @endif

                <li class="list-group-item"> {{ getLocaleString($operator->id, 'Volume Used') }} :
                    {{ ($customer->radaccts->sum('acctoutputoctets') +
                        $customer->radaccts->sum('acctinputoctets') +
                        $radaccts_history->sum('acctoutputoctets') +
                        $radaccts_history->sum('acctinputoctets')) /
                        1000 /
                        1000 /
                        1000 }}
                    GB
                </li>
            @endif
            {{-- Only for Hotspot Customer --}}

        </ul>

        @include('customers.footer-nav-links')

    </div>

@endsection

@section('pageJs')
@endsection
