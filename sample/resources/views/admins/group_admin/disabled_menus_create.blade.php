@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Customize your menu
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '50';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
    <h3>Customize your menu</h3>
@endsection

@section('content')
    <form class="" action="{{ route('disabled_menus.store') }}" method="POST">
        @csrf

        {{-- resellers_and_managers_group_admin --}}
        <div class="card">
            <div class="card-body">
                <div class="form-check">
                    <input type="checkbox" name="resellers_and_managers_group_admin" id="resellers_and_managers_group_admin"
                        @if ($disabled_menus->where('menu', 'resellers_and_managers_group_admin')->count() == 0) checked @endif data-bootstrap-switch>
                    <label class="form-check-label" for="resellers_and_managers_group_admin">
                        {{ config('sidebars.resellers_and_managers_group_admin') }}
                    </label>
                </div>
            </div>
        </div>
        {{-- resellers_and_managers_group_admin --}}

        {{-- Routers & Packages menu --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Routers & Packages menu</h3>
            </div>
            <div class="card-body">
                @php
                    $routers_packages_menus = ['ppp_ipv4_pools_group_admin', 'ppp_ipv6_pools_group_admin', 'ppp_profiles_group_admin'];
                @endphp

                @foreach ($routers_packages_menus as $routers_packages_menu)
                    <div class="form-check">
                        <input type="checkbox" name="{{ $routers_packages_menu }}" id="{{ $routers_packages_menu }}"
                            @if ($disabled_menus->where('menu', $routers_packages_menu)->count() == 0) checked @endif data-bootstrap-switch>
                        <label class="form-check-label" for="{{ $routers_packages_menu }}">
                            {{ config("sidebars.$routers_packages_menu") }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        {{-- Routers & Packages menu --}}

        {{-- recharge_card_menu_group_admin --}}
        <div class="card">
            <div class="card-body">
                <div class="form-check">
                    <input type="checkbox" name="recharge_card_menu_group_admin" id="recharge_card_menu_group_admin"
                        @if ($disabled_menus->where('menu', 'recharge_card_menu_group_admin')->count() == 0) checked @endif data-bootstrap-switch>
                    <label class="form-check-label" for="recharge_card_menu_group_admin">
                        {{ config('sidebars.recharge_card_menu_group_admin') }}
                    </label>
                </div>
            </div>
        </div>
        {{-- recharge_card_menu_group_admin --}}

        {{-- Customers menu --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Customers menu</h3>
            </div>
            <div class="card-body">
                @php
                    $customer_menus = ['online_customers_group_admin', 'offline_customers_group_admin', 'import_ppp_customers_group_admin', 'customer_zone_group_admin', 'devices_group_admin', 'custom_field_group_admin', 'btrc_report_group_admin', 'deleted_customers_group_admin'];
                @endphp

                @foreach ($customer_menus as $customer_menu)
                    <div class="form-check">
                        <input type="checkbox" name="{{ $customer_menu }}" id="{{ $customer_menu }}"
                            @if ($disabled_menus->where('menu', $customer_menu)->count() == 0) checked @endif data-bootstrap-switch>
                        <label class="form-check-label" for="{{ $customer_menu }}">
                            {{ config("sidebars.$customer_menu") }}
                        </label>
                    </div>
                @endforeach

            </div>
        </div>
        {{-- Customers menu --}}

        {{-- Bills and Payments --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bills and Payments menu</h3>
            </div>
            <div class="card-body">
                @php
                    $bills_and_payments_menus = ['verify_payments_group_admin', 'bills_summary_group_admin', 'due_notifier_group_admin', 'expiration_notifier_group_admin', 'payment_link_broadcast_group_admin'];
                @endphp

                @foreach ($bills_and_payments_menus as $bills_and_payments_menu)
                    <div class="form-check">
                        <input type="checkbox" name="{{ $bills_and_payments_menu }}" id="{{ $bills_and_payments_menu }}"
                            @if ($disabled_menus->where('menu', $bills_and_payments_menu)->count() == 0) checked @endif data-bootstrap-switch>
                        <label class="form-check-label" for="{{ $bills_and_payments_menu }}">
                            {{ config("sidebars.$bills_and_payments_menu") }}
                        </label>
                    </div>
                @endforeach

            </div>
        </div>
        {{-- Bills and Payments menu --}}

        {{-- incomes_expenses_menu_group_admin --}}
        <div class="card">
            <div class="card-body">
                <div class="form-check">
                    <input type="checkbox" name="incomes_expenses_menu_group_admin" id="incomes_expenses_menu_group_admin"
                        @if ($disabled_menus->where('menu', 'incomes_expenses_menu_group_admin')->count() == 0) checked @endif data-bootstrap-switch>
                    <label class="form-check-label" for="incomes_expenses_menu_group_admin">
                        {{ config('sidebars.incomes_expenses_menu_group_admin') }}
                    </label>
                </div>
            </div>
        </div>
        {{-- incomes_expenses_menu_group_admin --}}

        {{-- affiliate_program_menu_group_admin --}}
        <div class="card">
            <div class="card-body">
                <div class="form-check">
                    <input type="checkbox" name="affiliate_program_menu_group_admin" id="affiliate_program_menu_group_admin"
                        @if ($disabled_menus->where('menu', 'affiliate_program_menu_group_admin')->count() == 0) checked @endif data-bootstrap-switch>
                    <label class="form-check-label" for="affiliate_program_menu_group_admin">
                        {{ config('sidebars.affiliate_program_menu_group_admin') }}
                    </label>
                </div>
            </div>
        </div>
        {{-- affiliate_program_menu_group_admin --}}

        {{-- vat_menu_group_admin --}}
        <div class="card">
            <div class="card-body">
                <div class="form-check">
                    <input type="checkbox" name="vat_menu_group_admin" id="vat_menu_group_admin"
                        @if ($disabled_menus->where('menu', 'vat_menu_group_admin')->count() == 0) checked @endif data-bootstrap-switch>
                    <label class="form-check-label" for="vat_menu_group_admin">
                        {{ config('sidebars.vat_menu_group_admin') }}
                    </label>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-dark">
                    SUBMIT
                </button>
            </div>
        </div>
        {{-- vat_menu_group_admin --}}

    </form>
@endsection

@section('pageJs')
    <script>
        $(function() {
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            })
        })
    </script>
@endsection
