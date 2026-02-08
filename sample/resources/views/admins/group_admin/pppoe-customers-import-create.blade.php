@extends ('laraview.layouts.sideNavLayout')

@section('title')
Import PPP Customers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3> Import PPP Customers </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        {{-- alert --}}
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Alert!</h4>
            <p>
                To import users from Mikrotik, API user need full access.
            </p>
        </div>
        {{-- alert --}}

        <div class="row">

            <div class="col-sm-6">

                <form method="POST" action="{{ route('pppoe_customers_import.store') }}">

                    @csrf

                    <!--billing_profile_id-->
                    <div class="form-group">
                        <label for="billing_profile_id"><span class="text-danger">*</span>
                            On which day of every month customer will pay
                            @if (config('consumer.country_code') == 'BD')
                            (প্রতি মাসের কোন দিনে গ্রাহক পেমেন্ট করবেন)
                            @endif
                        </label>
                        <select class="form-control" id="billing_profile_id" name="billing_profile_id" required>
                            <option value="">Please select...</option>
                            @foreach ($billing_profiles->sortBy('billing_due_date') as $billing_profile)
                            <option value="{{ $billing_profile->id }}">{{ $billing_profile->due_date_figure }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/billing_profile_id-->

                    <!--nas_id-->
                    <div class="form-group">
                        <label for="nas_id"><span class="text-danger">*</span>Router</label>

                        <select class="form-control" id="nas_id" name="nas_id" required>

                            @foreach ($routers as $router)
                            <option value="{{ $router->id }}">{{ $router->location }} :: {{ $router->nasname }}</option>
                            @endforeach

                        </select>

                        @error('nas_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/nas_id-->

                    <!--operator_id-->
                    <div class="form-group">
                        <label for="operator_id"><span class="text-danger">*</span>Operator</label>
                        <select name="operator_id" id="operator_id" class="form-control select2" required>
                            @foreach ($operators as $operator )
                            <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/operator_id-->

                    <!--import_disabled_user-->
                    <div class="form-group">
                        <label for="import_disabled_user"><span class="text-danger">*</span>Import Disabled User</label>
                        <select name="import_disabled_user" id="import_disabled_user" class="form-control select2"
                            required>
                            <option value="no">no</option>
                            <option value="yes">yes</option>
                        </select>
                    </div>
                    <!--/import_disabled_user-->

                    <!--generate_bill-->
                    <div class="form-group">
                        <label for="generate_bill"><span class="text-danger">*</span>Generate Bill</label>
                        <select name="generate_bill" id="generate_bill" class="form-control select2" required>
                            <option value="no">no</option>
                            <option value="yes">yes</option>
                        </select>
                    </div>
                    <!--/generate_bill-->

                    <button type="submit" class="btn btn-dark">SUBMIT</button>

                </form>

            </div>
            <!--/col-sm-6-->

            <div class="col-sm-6">
                <dl>
                    {{-- Notes --}}
                    <dt><span class="text-danger">মনে রাখা জরুরিঃ </span></dt>
                    <dd>
                        <ul>
                            <li>
                                কাস্টমারগুলো সফলভাবে আপনার রাউটার থেকে সফটওয়্যার এ import হয়ে যাওয়ার পর, কাস্টমারগুলো
                                (PPP
                                Secret) আপনার রাউটার এ ডিসেবল হয়ে যাবে।
                                যেহেতু এখন থেকে কাস্টমারের অথেনটিকেশন রেডিয়াস সার্ভার এর মাধ্যমে হবে তাই কাস্টমারগুলো আর
                                রাউটার এ থাকার প্রোয়োজন নাই।
                            </li>
                            <li>
                                এখন থেকে আপনি কাস্টমার সফটওয়্যার এ তৈরি করবেন রাউটার এ নয়।
                            </li>
                            <li>
                                সফটওয়্যার এ কাস্টমার তৈরি করলে সেটা রাউটার এ সাথে সাথে তৈরি হবে না। 
                            </li>
                            <li>
                                কাস্টমারগুলো রাউটার এ ব্যাকআপ হিসেবে রাখার জন্য Customers menu থেকে ব্যাকআপ অন রাখুন।
                            </li>
                            <li>
                                কাস্টমারগুলো সফলভাবে import না হলে বা প্রয়োজনে যোগাযোগ করুনঃ <iframe width="190" height="30" style="border: 0" src="https://cdn.smooch.io/message-us/index.html?channel=whatsapp&color=teal&size=compact&radius=4px&label=Message us on WhatsApp&number=8801712552038"></iframe>
                            </li>
                        </ul>
                    </dd>
                    {{-- Notes --}}
                </dl>
            </div>
        </div>
        <!--/row-->

    </div>
    <!--/card-body-->

</div>

@endsection

@section('pageJs')
@endsection
