@extends ('laraview.layouts.sideNavLayout')

@section('title')
resellers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '10';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@section('contentTitle')

<ul class="nav flex-column flex-sm-row ml-4">
    <!--New Operator-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('sub_operators.create') }}">
            <i class="fas fa-plus"></i>
            New Reseller
        </a>
    </li>
    <!--/New Operator-->
</ul>

@endsection

@section('content')

<div class="card">

    <!--modal -->
    <div class="modal" tabindex="-1" role="dialog" id="modal-default">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body overflow-auto" id="ModalBody">
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /modal-content -->
        </div>
        <!-- /modal-dialog -->
    </div>
    <!-- /modal -->

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Company</th>
                    <th scope="col">Total User</th>
                    <th scope="col">Account Type</th>
                    <th scope="col">Balance</th>
                    <th scope="col">Status</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sub_operators as $sub_operator)
                <tr>
                    <td scope="row">{{ $sub_operator->id }}</td>
                    <td>{{ $sub_operator->name }}</td>
                    <td>{{ $sub_operator->company }}</td>
                    <td>{{ $sub_operator->customers()->count() }}</td>
                    <td>{{ $sub_operator->account_type_alias }}</td>
                    @if ($sub_operator->account_type == 'credit')
                    <td>{{ $sub_operator->credit_balance }}</td>
                    @else
                    <td>{{ $sub_operator->account_balance }}</td>
                    @endif
                    <td>{{ $sub_operator->status }}</td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                {{-- Details --}}
                                @can('view', $sub_operator)
                                <a class="dropdown-item"
                                    href="{{ route('sub_operators.show', ['sub_operator' => $sub_operator->id]) }}">
                                    Details
                                </a>
                                @endcan
                                {{-- Details --}}

                                {{-- Edit --}}
                                @can('update', $sub_operator)
                                <a class="dropdown-item"
                                    href="{{ route('sub_operators.edit', ['sub_operator' => $sub_operator->id]) }}">
                                    Edit
                                </a>
                                @endcan
                                {{-- Edit --}}

                                {{-- Add Balance --}}
                                @can('addBalance', $sub_operator)
                                <a class="dropdown-item"
                                    href="{{ route('sub_operators.account-balance.create', ['operator' => $sub_operator->id]) }}">
                                    <i class="fas fa-money-check-alt"></i>
                                    Add Balance
                                </a>
                                @endcan
                                {{-- Add Balance --}}

                                {{-- Entry Cash Received --}}
                                @can('entryCashReceived', $sub_operator)
                                <a class="dropdown-item"
                                    href="{{ route('accounts_receivable.cash_out.create', ['account' => $sub_operator->accountsProvides->where('account_owner', Auth::user()->id)->first()->id]) }}">
                                    <i class="fas fa-money-check-alt"></i>
                                    Entry Cash Received
                                </a>
                                @endcan
                                {{-- Entry Cash Received --}}

                                {{-- Edit Credit Limit --}}
                                @can('editLimit', $sub_operator)
                                <a class="dropdown-item"
                                    href="{{ route('sub_operators.credit-limit.create', ['operator' => $sub_operator->id]) }}">
                                    <i class="fas fa-money-check-alt"></i>
                                    Edit Credit Limit
                                </a>
                                @endcan
                                {{-- Edit Credit Limit --}}

                                {{-- Account Ledger --}}
                                @can('viewAccountLedger', $sub_operator)
                                @if ($sub_operator->account_type == 'credit')
                                {{-- credit => account provider --}}
                                <a class="dropdown-item"
                                    href="{{ route('account.transactions', ['account' => $sub_operator->accountsProvides->where('account_owner', Auth::user()->id)->first()->id]) }}">
                                    <i class="fas fa-book"></i>
                                    Account Ledger
                                </a>
                                @else
                                {{-- debit => account owner --}}
                                <a class="dropdown-item"
                                    href="{{ route('account.transactions', ['account' => $sub_operator->accountsOwns->where('account_provider', Auth::user()->id)->first()->id]) }}">
                                    <i class="fas fa-book"></i>
                                    Account Ledger
                                </a>
                                @endif
                                @endcan
                                {{-- Account Ledger --}}

                                {{-- Packages --}}
                                @can('assignPackages', $sub_operator)
                                <a class="dropdown-item"
                                    href="{{ route('operators.packages.index', ['operator' => $sub_operator->id]) }}">
                                    Packages
                                </a>
                                @endcan
                                {{-- Packages --}}

                                {{-- Billing Profiles --}}
                                @can('assignProfiles', $sub_operator)
                                <a class="dropdown-item" href="#"
                                    onclick="showBillingProfiles('{{ route('sub_operators.billing_profiles.index', ['operator' => $sub_operator->id]) }}')">
                                    Billing Profiles
                                </a>
                                @endcan
                                {{-- Billing Profiles --}}

                                {{-- Get Panel Access --}}
                                @can('getAccess', $sub_operator)
                                <a class="dropdown-item" href="#"
                                    onclick="getPanelAccess('{{ route('authenticate-operator-instance.show', ['operator' => $sub_operator->id]) }}')">
                                    Get Panel Access
                                </a>
                                @endcan
                                {{-- Get Panel Access --}}

                                {{-- Delete --}}
                                @can('delete', $sub_operator)
                                <a class="dropdown-item"
                                    href="{{ route('sub_operators.destroy.create', ['operator' => $sub_operator->id]) }}">
                                    Delete
                                </a>
                                @endcan
                                {{-- Delete --}}

                            </div>

                        </div>

                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    <!--/card body-->

</div>

@endsection

@section('pageJs')

<script>
    function showBillingProfiles(url)
    {
        $.get( url, function( data ) {
            $("#modal-title").html("Billing Profiles");
            $("#ModalBody").html(data);
            $('#modal-default').modal('show');
            modalDataTable();
        });
    }

    function getPanelAccess(url)
    {
        $.get( url, function( data ) {
            $("#modal-title").html("Get Panel Access");
            $("#ModalBody").html(data);
            $('#modal-default').modal('show');
        });
    }

</script>

@endsection