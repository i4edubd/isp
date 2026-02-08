@extends ('laraview.layouts.sideNavLayout')

@section('title', 'Operators')

@section('pageCss')
    <style>
        .card-body {
            padding: 2rem;
        }
        .modal-content {
            border-radius: 10px;
        }
        .table thead th {
            border-bottom: 2px solid #dee2e6;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-group .dropdown-menu {
            border-radius: 0.5rem;
        }
    </style>
@endsection

@section('activeLink')
    @php
        $active_menu = '1';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
    <div class="d-flex justify-content-between align-items-center">
        <h3>Operators</h3>
        <a class="btn btn-outline-success" href="{{ route('operators.create') }}">
            <i class="fas fa-plus"></i> New Operator
        </a>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="data_table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Company</th>
                        <th scope="col">Total User</th>
                        <th scope="col">Account Type</th>
                        <th scope="col">Balance</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($operators as $operator)
                        <tr>
                            <td>{{ $operator->id }}</td>
                            <td>{{ $operator->name }}</td>
                            <td>{{ $operator->company }}</td>
                            <td>{{ $operator->customers()->count() }}</td>
                            <td>{{ $operator->account_type_alias }}</td>
                            <td>{{ $operator->account_type == 'credit' ? $operator->credit_balance : $operator->account_balance }}</td>
                            <td>{{ $operator->status }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu">
                                        @if ($operator->deleting == 1)
                                            <a class="dropdown-item" href="#">Deleting...</a>
                                        @else
                                            @can('view', $operator)
                                                <a class="dropdown-item" href="{{ route('operators.show', ['operator' => $operator->id]) }}">Details</a>
                                            @endcan
                                            @can('update', $operator)
                                                <a class="dropdown-item" href="{{ route('operators.edit', ['operator' => $operator->id]) }}">Edit</a>
                                            @endcan
                                            @can('addBalance', $operator)
                                                <a class="dropdown-item" href="{{ route('operators.account-balance.create', ['operator' => $operator->id]) }}">
                                                    <i class="fas fa-money-check-alt"></i> Add Balance
                                                </a>
                                            @endcan
                                            @can('entryCashReceived', $operator)
                                                <a class="dropdown-item" href="{{ route('accounts_receivable.cash_out.create', ['account' => $operator->accountsProvides->where('account_owner', Auth::user()->id)->first()->id]) }}">
                                                    <i class="fas fa-money-check-alt"></i> Entry Cash Received
                                                </a>
                                            @endcan
                                            @can('editLimit', $operator)
                                                <a class="dropdown-item" href="{{ route('operators.credit-limit.create', ['operator' => $operator->id]) }}">
                                                    <i class="fas fa-money-check-alt"></i> Edit Credit Limit
                                                </a>
                                            @endcan
                                            @can('viewAccountLedger', $operator)
                                                @if ($operator->account_type == 'credit')
                                                    <a class="dropdown-item" href="{{ route('account.transactions', ['account' => $operator->accountsProvides->where('account_owner', Auth::user()->id)->first()->id]) }}">
                                                        <i class="fas fa-book"></i> Account Ledger
                                                    </a>
                                                @else
                                                    <a class="dropdown-item" href="{{ route('account.transactions', ['account' => $operator->accountsOwns->where('account_provider', Auth::user()->id)->first()->id]) }}">
                                                        <i class="fas fa-book"></i> Account Ledger
                                                    </a>
                                                @endif
                                            @endcan
                                            @can('assignPackages', $operator)
                                                <a class="dropdown-item" href="{{ route('operators.master_packages.index', ['operator' => $operator->id]) }}">Packages</a>
                                            @endcan
                                            @can('assignProfiles', $operator)
                                                <a class="dropdown-item" href="#" onclick="showBillingProfiles('{{ route('operators.billing_profiles.index', ['operator' => $operator->id]) }}')">Billing Profiles</a>
                                            @endcan
                                            @can('assignSpecialPermission', $operator)
                                                <a class="dropdown-item" href="#" onclick="showSpecialPermissions('{{ route('operators.special-permission.index', ['operator' => $operator->id]) }}')">Special Permissions</a>
                                            @endcan
                                            @can('getAccess', $operator)
                                                <a class="dropdown-item" href="#" onclick="getPanelAccess({{ $operator->id }})">Get Panel Access</a>
                                            @endcan
                                            @can('suspend', $operator)
                                                <a class="dropdown-item" href="{{ route('operators.suspend.create', ['operator' => $operator->id]) }}">Suspend</a>
                                            @endcan
                                            @can('activate', $operator)
                                                <form method="post" action="{{ route('operators.activate.store', ['operator' => $operator->id]) }}">
                                                    @csrf
                                                    <button class="dropdown-item" type="submit">Activate</button>
                                                </form>
                                            @endcan
                                            @can('delete', $operator)
                                                <a class="dropdown-item" href="{{ route('operators.destroy.create', ['operator' => $operator->id]) }}">Delete</a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body overflow-auto" id="ModalBody"></div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Modal -->
@endsection

@section('pageJs')
<script src="/jsPlugins/chart.js-3.7.0/package/dist/chart.min.js"></script>
<script src="/jsPlugins/chartjs-plugin-datalabels/chartjs-plugin-datalabels.min.js"></script>

<script>
    function showBillingProfiles(url) {
        $.get(url, function(data) {
            $("#modal-title").html("Billing Profiles");
            $("#ModalBody").html(data);
            $('#modal-default').modal('show');
        });
    }

    function showSpecialPermissions(url) {
        $.get(url, function(data) {
            $("#modal-title").html("Special Permissions");
            $("#ModalBody").html(data);
            $('#modal-default').modal('show');
        });
    }

    function getPanelAccess(operator) {
        $.get("/admin/authenticate-operator-instance/" + operator, function(data) {
            $("#modal-title").html("Get Panel Access");
            $("#ModalBody").html(data);
            $('#modal-default').modal('show');
        });
    }
</script>
@endsection