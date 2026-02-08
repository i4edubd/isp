@section('contentTitle')
<h3> Due Notifier </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">


                <form method="POST"
                    action="{{ route('due_date_reminders.notification.store', ['due_date_reminder' => $due_date_reminder->id]) }}">

                    @csrf

                    <!--customers_count-->
                    <div class="form-group">

                        <label for="customers_count">Number of Recipient</label>

                        <input type="text" class="form-control" id="customers_count" value="{{ $customers_count }}"
                            disabled>

                    </div>
                    <!--/customers_count-->

                    <!--text_message-->
                    <div class="form-group">
                        <label for="text_message">Text Message</label>
                        <textarea class="form-control" id="text_message" rows="3"
                            disabled>{{ $due_date_reminder->message }}</textarea>
                    </div>
                    <!--/text_message-->

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
