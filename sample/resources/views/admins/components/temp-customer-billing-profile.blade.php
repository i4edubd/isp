@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST"
                    action="{{ route('temp_customer.billing_profile.store', ['temp_customer' => $temp_customer->id]) }}">

                    @csrf

                    <!--billing_profile_id-->
                    <div class="form-group">
                        <label for="billing_profile_id"><span class="text-danger">*</span>
                            Last Payment Date
                        </label>
                        <select class="form-control" id="billing_profile_id" name="billing_profile_id" selected>
                            @if ($selected_profile)
                            <option value="{{ $selected_profile->id }}" selected>
                                {{ $selected_profile->due_date_figure }}
                            </option>
                            @endif
                            @foreach ($billing_profiles->sortBy('billing_due_date') as $billing_profile)
                            <option value="{{ $billing_profile->id }}">{{ $billing_profile->due_date_figure }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/billing_profile_id-->

                    <button type="submit" class="btn btn-dark">NEXT<i class="fas fa-arrow-right"></i></button>

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