@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Payment Gateways
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '1';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.developer.sidebar')
@endsection


@section('contentTitle')
    <ul class="nav flex-column flex-sm-row ml-4">
        <!--New Operator-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('payment_gateways.create') }}">
                <i class="fas fa-plus"></i>
                New Gateway
            </a>
        </li>
        <!--/New Operator-->
    </ul>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <table id="data_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" style="width: 2%">#</th>
                        <th scope="col">Operator</th>
                        <th scope="col">provider</th>
                        <th scope="col">Payment Method</th>
                        <th scope="col">Credentials Path</th>
                        <th scope="col">Inheritable</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($payment_gateways as $payment_gateway)
                        <tr>
                            <th scope="row">{{ $payment_gateway->id }}</th>
                            <td>{{ $payment_gateway->operator->id }} :: {{ $payment_gateway->operator->role }} ::
                                {{ $payment_gateway->operator->name }} :: {{ $payment_gateway->operator->company }}</td>
                            <td>{{ $payment_gateway->provider_name }}</td>
                            <td>{{ $payment_gateway->payment_method }}</td>
                            <td>{{ $payment_gateway->credentials_path }}</td>
                            <td>{{ $payment_gateway->inheritable }}</td>
                            <td>

                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <a class="dropdown-item"
                                            href="{{ route('payment_gateways.edit', ['payment_gateway' => $payment_gateway->id]) }}">
                                            <i class="fas fa-pencil-alt"></i>
                                            Edit
                                        </a>
                                        <form method="post"
                                            action="{{ route('payment_gateways.destroy', ['payment_gateway' => $payment_gateway->id]) }}"
                                            onsubmit="return confirm('Are you sure to Delete')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('pageJs')
@endsection
