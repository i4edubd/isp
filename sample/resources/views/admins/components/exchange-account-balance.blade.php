@section('contentTitle')
<h3>Exchange Balance</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item"><a href="{{ $previous_url }}">{{ $breadcrumb_label }}</a></li>
    <li class="breadcrumb-item active">Exchange Balance</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table class="table table-hover">

            <thead>

                <tr>
                    <th scope="col">Account</th>
                    <th scope="col">Exchange Amount</th>
                    <th scope="col">Balance(Before Exchange)</th>
                    <th scope="col">Balance(After Exchange)</th>
                </tr>

            </thead>

            <tbody>

                <tr>
                    <td>{{ $account->owner->id }} :: {{ $account->owner->name }}</td>
                    <td>{{ $exchange_amount }}</td>
                    <td>{{ $account->balance }}</td>
                    <td>{{ $account->balance - $exchange_amount }}</td>
                </tr>

                <tr>
                    <td>{{ $exchange_account->owner->id }} :: {{ $exchange_account->owner->name }}</td>
                    <td>{{ $exchange_amount }}</td>
                    <td>{{ $exchange_account->balance }}</td>
                    <td>{{ $exchange_account->balance - $exchange_amount }}</td>
                </tr>

            </tbody>

        </table>

        <!-- form start -->
        <div class="card-footer">
            <form method="POST" action="{{ route('accounts.exchange.store', ['account' => $account->id]) }}">
                @csrf
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

    </div>

</div>

@endsection

@section('pageJs')
@endsection