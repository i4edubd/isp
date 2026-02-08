@section('contentTitle')
<h3> Invoices Download </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST" action="{{ route('customers-invoice-download.store') }}">

                    @csrf

                    <!--File format-->
                    <div class="form-group">
                        <label for="file_format"><span class="text-danger">*</span>File Format</label>
                        <select class="form-control" id="file_format" name="file_format" required>
                            <option value="">Please select...</option>
                            <option value="PDF">PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                    <!--/File format-->

                    <!--sortby-->
                    <div class="form-group">
                        <label for="sortby">Sort By</label>
                        <select class="form-control" id="sortby" name="sortby">
                            <option value="">Please select...</option>
                            <option value="username">Customer Username</option>
                            <option value="customer_id">Customer ID</option>
                            <option value="package_id">Package ID</option>
                            <option value="customer_zone_id">Customer Zone ID</option>
                        </select>
                    </div>
                    <!--/sortby-->

                    <!--customer_zone_id-->
                    <div class="form-group">
                        <label for="customer_zone_id">Customer Zone (Optional)</label>
                        <select class="form-control" id="customer_zone_id" name="customer_zone_id">
                            <option value="">Please select...</option>
                            @foreach ($customer_zones->sortBy('name') as $customer_zone)
                            <option value="{{ $customer_zone->id }}">{{ $customer_zone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/customer_zone_id-->

                    <!--package_id-->
                    <div class="form-group">
                        <label for="package_id">Package (Optional)</label>
                        <select class="form-control" id="package_id" name="package_id">
                            <option value="">Please select...</option>
                            @foreach ($packages->sortBy('name') as $package)
                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/package_id-->

                    <!--date-->
                    <div class='form-group'>
                        <label for='datepicker'>Date</label>
                        <input type='text' name='date' id='datepicker' class='form-control'>
                    </div>
                    <!--/date-->

                    {{-- year --}}
                    <div class="form-group">
                        <label for="year">year (Optional)</label>
                        <select name="year" id="year" class="form-control">
                            <option value=''>please select...</option>
                            @php
                            $start = date(config('app.year_format'));
                            $stop = $start - 5;
                            @endphp
                            @for($i = $start; $i >= $stop; $i--)
                            <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                    {{--year --}}

                    {{-- month --}}
                    <div class="form-group">
                        <label for="month">Month (Optional)</label>
                        <select name="month" id="month" class="form-control">
                            <option value=''>please select...</option>
                            <option value='January'>January</option>
                            <option value='February'>February</option>
                            <option value='March'>March</option>
                            <option value='April'>April</option>
                            <option value='May'>May</option>
                            <option value='June'>June</option>
                            <option value='July'>July</option>
                            <option value='August'>August</option>
                            <option value='September'>September</option>
                            <option value='October'>October</option>
                            <option value='November'>November</option>
                            <option value='December'>December</option>
                        </select>
                    </div>
                    {{--month --}}

                    {{-- operator --}}
                    @if (Auth::user()->role == 'group_admin' || Auth::user()->role == 'operator')
                    <div class="form-group">
                        <label for="operator_id">operator (Optional)</label>
                        <select name="operator_id" id="operator_id" class="form-control">
                            <option value=''>please select...</option>
                            @foreach (Auth::user()->operators->where('role', '!=', 'manager') as $operator)
                            <option value="{{ $operator->id }}">
                                {{ $operator->id }} :: {{ $operator->name }} :: {{ $operator->readable_role }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    {{--operator --}}

                    <button type="submit" class="btn btn-dark">Download</button>

                </form>

            </div>
            <!--/col-sm-6-->

        </div>
        <!--/row-->

    </div>
    <!--/card-body-->

</div>

@endsection

@section('pageJs')

<script>
    $(function() {
        $('#datepicker').datepicker({
            autoclose: !0
        });
    });
</script>

@endsection
