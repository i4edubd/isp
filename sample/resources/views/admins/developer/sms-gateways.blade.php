@extends ('laraview.layouts.sideNavLayout')

@section('title')
    SMS Gateways
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '2';
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
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('sms_gateways.create') }}">
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
                        <th scope="col">Country Code</th>
                        <th scope="col">provider</th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Saleable</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($sms_gateways as $sms_gateway)
                        <tr>
                            <td scope="row">{{ $sms_gateway->id }}</td>
                            <td>{{ $sms_gateway->operator_id }} :: {{ $sms_gateway->operator->role }} ::
                                {{ $sms_gateway->operator->company }}</td>
                            <td>{{ $sms_gateway->country_code }}</td>
                            <td>{{ $sms_gateway->provider_name }}</td>
                            <td>{{ $sms_gateway->unit_price }}</td>
                            <td>{{ $sms_gateway->saleable }}</td>

                            <td>

                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <a class="dropdown-item"
                                            href="{{ route('sms_gateways.edit', ['sms_gateway' => $sms_gateway->id]) }}">
                                            <i class="fas fa-pencil-alt"></i>
                                            Edit
                                        </a>
                                        <form method="post"
                                            action="{{ route('sms_gateways.destroy', ['sms_gateway' => $sms_gateway->id]) }}"
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
