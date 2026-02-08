@section('contentTitle')
<h3>Edit Device</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST" action="{{ route('devices.update', ['device' => $device->id]) }}">

                    @csrf

                    @method('put')

                    <!--name-->
                    <div class="form-group">
                        <label for="name"><span class="text-danger">*</span>Device Name</label>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" value="{{ $device->name }}" autocomplete="off" required>
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
                            id="location" value="{{ $device->location }}" autocomplete="off" required>
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
