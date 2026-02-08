@section('contentTitle')
<h3>New Device</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST" action="{{ route('devices.store') }}">

                    @csrf

                    <!--name-->
                    <div class="form-group">
                        <label for="name"><span class="text-danger">*</span>Device Name</label>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" value="{{ old('name') }}" autocomplete="name" required>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/name-->

                    <!--location-->
                    <div class="form-group">
                        <label for="location"><span class="text-danger">*</span>Device Location</label>
                        <input name="location" type="text" class="form-control @error('location') is-invalid @enderror"
                            id="location" value="{{ old('location') }}" autocomplete="location" required>
                        @error('location')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/location-->

                    <button type="submit" class="btn btn-dark">SUBMIT</button>

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
@endsection
