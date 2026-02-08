@section('contentTitle')
    <h3> Due Notifier </h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <div class="row">

                <div class="col-sm-6">

                    <form method="POST" action="{{ route('due-date.due-notifier.store', ['due_date' => $due_date]) }}">

                        @csrf

                        <!--bills_count-->
                        <div class="form-group">

                            <label for="bills_count">Number of Recipient</label>

                            <input type="text" class="form-control" id="bills_count" value="{{ $bills_count }}" disabled>

                        </div>
                        <!--/bills_count-->

                        <!--text_message-->
                        <div class="form-group">
                            <label for="text_message">Text Message</label>
                            <textarea class="form-control" id="text_message" rows="3" disabled>{{ $sms }}</textarea>
                        </div>
                        <!--/text_message-->

                        <button type="submit" class="btn btn-dark">Send Message</button>

                        <a class="ml-2 btn btn-primary" href="{{ route('event_sms.edit', ['event_sms' => $event_sms]) }}"
                            role="button">Edit Message</a>

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
