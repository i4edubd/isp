@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Mandatory Customers Attributes
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '5';
        $active_link = '11';
    @endphp
@endsection

@section('sidebar')
    @include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
    <h3> Mandatory Fields Setting </h3>
@endsection

@section('content')
    <form action="{{ route('mandatory_customers_attributes.store') }}" method="post">

        @csrf

        <div class="card">

            <div class="card-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Field Name</th>
                            <th scope="col">Mandatory ?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers_attributes as $key => $value)
                            <tr>
                                <td>{{ $value }}</td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $key }}"
                                            id="{{ $key }}" value="Yes" @checked(isMandatoryCustomerAttribute($key, Auth::user()))>
                                        <label class="form-check-label" for="{{ $key }}">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $key }}"
                                            id="{{ $key }}" value="No" @checked(isMandatoryCustomerAttribute($key, Auth::user()) == false)>
                                        <label class="form-check-label" for="{{ $key }}">No</label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-dark">Update</button>
            </div>

        </div>

    </form>
@endsection

@section('pageJs')
@endsection
