@section('contentTitle')
    <h3>New Card Distributor</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <div class="row">
                <div class="col-sm-6">
                    <form id="quickForm" method="POST" action="{{ route('card_distributors.store') }}" autocomplete="off">
                        <input autocomplete="false" name="hidden" type="text" style="display:none;">
                        @csrf

                        <!--name-->
                        <div class="form-group">
                            <label for="name"><span class="text-danger">*</span>Name</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ old('name') }}" autocomplete="name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/name-->

                        <!--mobile-->
                        <div class="form-group">
                            <label for="mobile"><span class="text-danger">*</span>Mobile</label>
                            <input name="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror"
                                id="mobile" value="{{ old('mobile') }}" autocomplete="mobile" required>
                            @error('mobile')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/mobile-->

                        <!-- account_type -->
                        <div class="form-group">
                            <label for="account_type"><span class="text-danger">*</span>Account Type</label>
                            <select name="account_type" id="account_type" class="form-control" required>
                                <option value="">Please Select...</option>
                                <option value="prepaid">prepaid</option>
                                <option value="postpaid">postpaid</option>
                            </select>
                        </div>
                        <!--/account_type -->

                        <!--email-->
                        <div class="form-group">
                            <label for="email"><span class="text-danger">*</span>Email address</label>
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" value="{{ old('email') }}" autocomplete="off" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/email-->

                        <!--password-->
                        <div class="form-group">
                            <label for="password"><span class="text-danger">*</span>Password</label>
                            <input name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                placeholder="Password" autocomplete="off" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--password-->

                        <!--store_name-->
                        <div class="form-group">
                            <label for="store_name"><span class="text-danger">*</span>Store Name</label>
                            <input name="store_name" type="text"
                                class="form-control @error('store_name') is-invalid @enderror" id="store_name"
                                value="{{ old('store_name') }}" autocomplete="store_name" required>
                            @error('store_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/store_name-->

                        {{-- store_address --}}
                        <div class="form-group">
                            <label for="store_address"><span class="text-danger">*</span>Store Address</label>
                            <textarea name="store_address" class="form-control" id="store_address" rows="3" required></textarea>
                        </div>
                        {{-- store_address --}}

                        <button type="submit" class="btn btn-dark">Submit</button>

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
