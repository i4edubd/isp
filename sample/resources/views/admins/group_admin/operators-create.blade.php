@extends ('laraview.layouts.sideNavLayout')

@section('title')
    New Operator
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '1';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
    <h3>New Operator</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <div class="row">
                <div class="col-sm-6">
                    <form id="quickForm" autocomplete="off" method="POST" action="{{ route('operators.store') }}"
                        onsubmit="disableDuplicateSubmit()">
                        @csrf
                       
                        <!--company-->
                        <div class="form-group">
                            <label for="company"><span class="text-danger">*</span>Company Name</label>
                            <input name="company" type="text" class="form-control @error('company') is-invalid @enderror"
                                id="company" value="{{ Auth::user()->company }}" autocomplete="new-password" required>
                            @error('company')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/company-->

                        <!--company_in_native_lang-->
                        <div class="form-group">
                            <label for="company_in_native_lang"><span class="text-danger">*</span>
                                Company Name in {{ getLanguage(Auth::user())->name_native }}
                            </label>
                            <input name="company_in_native_lang" type="text"
                                class="form-control @error('company_in_native_lang') is-invalid @enderror"
                                id="company_in_native_lang" value="{{ Auth::user()->company_in_native_lang }}"
                                autocomplete="new-password" required>
                            @error('company_in_native_lang')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/company_in_native_lang-->
                        
                        {{-- country_id --}}
                        <div class="form-group">
                            <label for="country_id"><span class="text-danger">*</span>Country</label>
                            <select class="form-control" id="country_id" name="country_id"
                                hx-get="{{ route('ajax.timezones') }}" hx-target="#timezone" required>
                                <option value="">select...</option>
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
                            </select>
                        </div>
                        {{-- timezone --}}

                        <!-- account_type -->
                        <div class="form-group">
                            <label for="account_type"><span class="text-danger">*</span>Account Type</label>
                            <select name="account_type" id="account_type" class="form-control"
                                onchange="showAccountTypeOption(this.value)" required>
                                <option value="">Please Select...</option>
                                <option value="credit">Postpaid</option>
                                <option value="debit">Prepaid</option>
                            </select>
                        </div>
                        <!--/account_type -->

                        <!--account_type_option-->
                        <div id="account_type_option">
                        </div>
                        <!--/account_type_option-->

                        <!--name-->
                    <div class="form-group">
                            <label for="name"><span class="text-danger">*</span>Name</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                 id="name" value="{{ old('name') }}" autocomplete="new-password" pattern="^\S+\s+\S+.*$" required>
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
                                id="mobile" value="{{ old('mobile') }}" autocomplete="new-password" required>
                            @error('mobile')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/mobile-->

                        <!--email-->
                        <div class="form-group">
                            <label for="email"><span class="text-danger">*</span>Email address</label>
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" value="{{ old('email') }}" autocomplete="new-password" required>
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
                                placeholder="Password" autocomplete="new-password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--password-->

                        <button type="submit" id="submit-button" class="btn btn-dark">Submit</button>

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
    <script src="/js/htmx.min.js"></script>
    <script type="text/javascript">
        function showAccountTypeOption(account_type) {
            let url = "/admin/options-for-account-type?account_type=" + account_type;
            $.get(url, function(data) {
                $("#account_type_option").html(data);
            });

        }
    </script>
@endsection
