@section('contentTitle')
@include('admins.components.account-title')
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item"><a href="{{ $previous_url }}"> {{ $breadcrumb_label }}</a></li>
    <li class="breadcrumb-item">
        <a href="{{ route('account.transactions', ['account' => $account->id]) }}">
            Transactions
        </a>
    </li>
    <li class="breadcrumb-item active">Cash Out Details</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <!--modal -->
        <div class="modal fade" id="modal-default">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalTitle"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="ModalBody">
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

        <table id="data_table" class="table table-bordered table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Old Balance</th>
                    <th>New Balance</th>
                    <th>Description</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Bank TxnID</th>
                    <th>Processor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($account_outs as $expense)
                <tr>
                    <td>{{ $expense->date }}</td>
                    <td>{{ $expense->amount }}</td>
                    <td>{{ $expense->old_balance }}</td>
                    <td>{{ $expense->new_balance }}</td>
                    <td>
                        <a href="#" onclick="showAccountOutDetails('{{ $expense->id }}')">
                            {{ $expense->description }}
                        </a>
                    </td>
                    <td>{{ $expense->name }}</td>
                    <td>{{ $expense->username }}</td>
                    @if ($expense->transaction)
                    <td>{{ $expense->transaction->bank_txnid }}</td>
                    <td>{{ $expense->transaction->card_type }}</td>
                    @else
                    <td></td>
                    <td></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@endsection

@section('pageJs')

<script>
    function showAccountOutDetails(cash_out)
    {
        $.get( "/admin/cash_outs/" + cash_out, function( data ) {
            $("#ModalTitle").html("Transaction Details");
            $('#modal-default').modal('show');
            $("#ModalBody").html(data);
        });
    }
</script>

@endsection