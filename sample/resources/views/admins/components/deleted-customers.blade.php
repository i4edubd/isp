@section('contentTitle')
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('deleted_customers.index') }}" method="get">

    {{-- connection_type --}}
    <div class="form-group col-md-2">
        <select name="connection_type" id="connection_type" class="form-control">
            <option value=''>connection type...</option>
            <option value='PPPoE'>PPP</option>
            <option value='Hotspot'>Hotspot</option>
            <option value='StaticIp'>StaticIp</option>
        </select>
    </div>
    {{--connection_type --}}

    {{-- zone_id --}}
    <div class="form-group col-md-2">
        <select name="zone_id" id="zone_id" class="form-control">
            <option value=''>zone...</option>
            @foreach (Auth::user()->customer_zones->sortBy('name') as $customer_zone)
            <option value="{{ $customer_zone->id }}">{{ $customer_zone->name }}</option>
            @endforeach
        </select>
    </div>
    {{--zone_id --}}

    {{-- package_id --}}
    <div class="form-group col-md-2">
        <select name="package_id" id="package_id" class="form-control">
            <option value=''>package...</option>
            @foreach (Auth::user()->allPackages->groupBy('operator_id') as $gpackages)
            @foreach ($gpackages->sortBy('name') as $package)
            <option value="{{ $package->id }}">{{ $package->operator->name }} :: {{ $package->name }}</option>
            @endforeach
            @endforeach
        </select>
    </div>
    {{--package_id --}}

    {{-- billing_profile_id --}}
    <div class="form-group col-md-2">
        <select name="billing_profile_id" id="billing_profile_id" class="form-control">
            <option value=''>Billing Profile...</option>
            @foreach (Auth::user()->billing_profiles->sortBy('name') as $billing_profile)
            <option value="{{ $billing_profile->id }}">{{ $billing_profile->name }}</option>
            @endforeach
        </select>
    </div>
    {{--billing_profile_id --}}

    {{-- Page length --}}
    <div class="form-group col-md-2">
        <select name="length" id="length" class="form-control">
            <option value="{{ $length }}" selected>Show {{ $length }} entries </option>
            <option value="10">Show 10 entries</option>
            <option value="25">Show 25 entries</option>
            <option value="50">Show 50 entries</option>
            <option value="100">Show 100 entries</option>
        </select>
    </div>
    {{--Page length --}}

    {{-- username --}}
    <div class="form-group col-md-2">
        <input type="text" name="username" id="username" class="form-control" placeholder="username LIKE ...">
    </div>
    {{-- username --}}

    {{-- operator --}}
    @if (Auth::user()->role == 'group_admin' || Auth::user()->role == 'operator')
    <div class="form-group col-md-2">
        <select name="operator_id" id="operator_id" class="form-control">
            <option value=''>operator...</option>
            @foreach (Auth::user()->operators->where('role', '!=', 'manager') as $operator)
            <option value="{{ $operator->id }}"> {{ $operator->name }} </option>
            @endforeach
        </select>
    </div>
    @endif
    {{--operator --}}

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>

{{-- @endFilter --}}

<div class="card">

    <div class="card-body">

        <table id="phpPaging" class="table table-bordered">

            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Username</th>
                    <th scope="col">Password</th>
                    <th scope="col"></th>
                </tr>
            </thead>

            <tbody>

                @foreach ($customers as $customer )
                <tr>
                    <td scope="row">{{ $customer->id }}</td>
                    <td>{{ $customer->mobile }}</td>
                    <td> {{ $customer->username }}</td>
                    <td>{{ $customer->password }}</td>
                    <td class="d-sm-flex">
                        <form method="post"
                            action="{{ route('deleted_customers.destroy', ['deleted_customer' => $customer->id]) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-restore"></i>
                                Restore
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach

            </tbody>

        </table>

    </div>

    <div class="card-footer">
        <div class="row">

            <div class="col-sm-2">
                Total Entries: {{ $customers->total() }}
            </div>

            <div class="col-sm-6">
                {{ $customers->links() }}
            </div>

        </div>
    </div>
    <!--/card-footer-->

</div>

@endsection

@section('pageJs')
@endsection