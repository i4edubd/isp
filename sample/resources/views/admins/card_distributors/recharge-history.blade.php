@extends ('laraview.layouts.sideNavLayoutForCardDistributors')

@section('title')
    Recharge History
@endsection

@section('activeLink')
    @php
        $active_menu = '2';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.card_distributors.sidebar')
@endsection

@section('contentTitle')
    <div class="row">
        <div class="col-md-6">
            <h3> Recharge History </h3>
        </div>
        <div class="col-md-6">
            <form action="{{ route('card.recharge-history') }}" method="GET" onsubmit="return disableDuplicateSubmit()">
                <div class="input-group">
                    <input type="search" maxlength="20" name="pin" class="form-control form-control-lg"
                        placeholder="PIN Number" required>
                    <div class="input-group-append">
                        <button type="submit" id="submit-button" class="btn btn-lg btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <table id="data_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">package</th>
                        <th scope="col">PIN</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Commission</th>
                        <th scope="col">Recharge Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recharge_cards as $recharge_card)
                        <tr>
                            <td>{{ $recharge_card->package->name }}</td>
                            <td>{{ $recharge_card->pin }}</td>
                            <td>{{ $recharge_card->mobile }}</td>
                            <td>{{ $recharge_card->package->price }}</td>
                            <td>{{ $recharge_card->commission }}</td>
                            <td>{{ $recharge_card->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
