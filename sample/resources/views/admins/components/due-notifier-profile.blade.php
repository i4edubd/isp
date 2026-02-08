@section('contentTitle')
<h3> Due Notifier </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST" action="{{ route('due-notifier.store') }}">

                    @csrf

                    <!--due_date-->
                    <div class='form-group'>
                        <label for='due_date'><span class="text-danger">*</span>Payment Date</label>
                        <select class="form-control" id="due_date" name="due_date" required>
                            @foreach ($payment_dates as $date => $customer_count)
                            <option value="{{ $date }}">Date: {{ $date }}, Customer count: {{ $customer_count }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <!--/due_date-->

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
