@section('contentTitle')
    <h3>Edit Biller | Email: {{ $operator->email }} </h3>
@endsection

@section('content')
    <div class="card">

        <p class="text-danger">* required field</p>

        <form method="POST" action="{{ route('operators.profile.store', ['operator' => $operator->id]) }}"
            enctype="multipart/form-data" onsubmit="disableDuplicateSubmit()">

            @csrf

            <div class="card-body">

                <div class="row">

                    <div class="col-6">

                        <!--company-->
                        @can('editCompany', $operator)
                            <div class="form-group">

                                <label for="company"><span class="text-danger">*</span>Company Name</label>
                                <input name="company" type="text" class="form-control @error('company') is-invalid @enderror"
                                    id="company" value="{{ $operator->company }}" required>

                                @error('company')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>

                            <div class="form-group">

                                <label for="company_in_native_lang"><span class="text-danger">*</span>
                                    Company Name in {{ getLanguage(Auth::user())->name }}
                                </label>
                                <input name="company_in_native_lang" type="text"
                                    class="form-control @error('company_in_native_lang') is-invalid @enderror"
                                    id="company_in_native_lang" value="{{ $operator->company_in_native_lang }}" required>

                                @error('company_in_native_lang')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        @else
                            <div class="form-group">
                                <label for="company">Company Name</label>
                                <input type="text" class="form-control" id="company" value="{{ $operator->company }}"
                                    disabled>
                            </div>

                            <div class="form-group">
                                <label for="company_in_native_lang">
                                    Company Name in {{ getLanguage(Auth::user())->name }}
                                </label>
                                <input type="text" class="form-control" id="company_in_native_lang"
                                    value="{{ $operator->company_in_native_lang }}" disabled>
                            </div>
                        @endcan
                        <!--/company-->

                        <!--mobile-->
                        <div class="form-group">

                            <label for="mobile"><span class="text-danger">*</span>Mobile</label>
                            <input name="mobile" type="text" max="254"
                                class="form-control @error('mobile') is-invalid @enderror" id="mobile"
                                value="{{ $operator->mobile }}" required>

                            @error('mobile')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <!--/helpline-->

                        <!--helpline-->
                        <div class="form-group">
                            <label for="helpline"><span class="text-danger">*</span>Helpline</label>
                            <input name="helpline" type="text" max="254"
                                class="form-control @error('helpline') is-invalid @enderror" id="helpline"
                                value="{{ $operator->helpline }}" required>

                            @error('helpline')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <!--/helpline-->

                        <!--house_no-->
                        <div class="form-group">
                            <label for="house_no">#House</label>
                            <input name="house_no" type="text"
                                class="form-control @error('house_no') is-invalid @enderror" id="house_no"
                                value="{{ $operator->house_no }}">

                            @error('house_no')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <!--/house_no-->

                        <!--road_no-->
                        <div class="form-group">
                            <label for="road_no">#Street Name/Number</label>
                            <input name="road_no" type="text" class="form-control @error('road_no') is-invalid @enderror"
                                id="road_no" value="{{ $operator->road_no }}">

                            @error('road_no')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <!--/road_no-->

                    </div>

                    <div class="col-6">

                        <!--district-->
                        <div class="form-group">
                            <label for="district"><span class="text-danger">*</span>District</label>
                            <input name="district" type="text"
                                class="form-control @error('district') is-invalid @enderror" id="district"
                                value="{{ $operator->district }}" required>

                            @error('district')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <!--/district-->

                        <!--postal_code-->
                        <div class="form-group">
                            <label for="postal_code"><span class="text-danger">*</span>Postal Code</label>
                            <input name="postal_code" type="text"
                                class="form-control @error('postal_code') is-invalid @enderror" id="postal_code"
                                value="{{ $operator->postal_code }}" required>

                            @error('postal_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <!--/postal_code-->

                        {{-- country_id --}}
                        <div class="form-group">
                            <label for="country_id"><span class="text-danger">*</span>Country</label>
                            <select class="form-control" id="country_id" name="country_id"
                                hx-get="{{ route('ajax.timezones') }}" hx-target="#timezone" required>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- country_id --}}

                        {{-- lang_code --}}
                        <div class="form-group">
                            <label for="lang_code"><span class="text-danger">*</span>Language</label>
                            <select class="form-control" id="lang_code" name="lang_code" required>
                                @foreach ($languages as $language)
                                    <option value="{{ $language->code }}">{{ $language->name }}
                                        ({{ $language->name_native }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- lang_code --}}

                        {{-- timezone --}}
                        <div class="form-group">
                            <label for="timezone"><span class="text-danger">*</span>Time zone</label>
                            <select class="form-control" id="timezone" name="timezone" required>
                                <option value="{{ Auth::user()->timezone }}">{{ Auth::user()->timezone }}</option>
                            </select>
                        </div>
                        {{-- timezone --}}

                        <!--company_logo-->
                        <div class="form-group row">
                            <label for="company_logo">Logo</label>
                            <div class="custom-file">
                                <input type="file" name="company_logo" class="custom-file-input" id="company_logo">
                                <label class="custom-file-label" for="company_logo">Choose file</label>
                            </div>
                        </div>
                        <!--/company_logo-->

                    </div>

                </div>

            </div>
            <!--/Card Body-->

            <div class="card-footer">
                <button type="submit" id="submit-button" class="btn btn-dark">Submit</button>
            </div>
            <!--/card-footer-->

        </form>

    </div>
@endsection

@section('pageJs')
    <script src="/js/htmx.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
