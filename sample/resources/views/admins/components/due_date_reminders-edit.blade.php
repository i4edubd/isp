@section('contentTitle')
    <h3>Edit Due Date Reminder</h3>
@endsection

@section('content')
    <div class="card">

        <form method="POST"
            action="{{ route('due_date_reminders.update', ['due_date_reminder' => $due_date_reminder->id]) }}">

            @csrf

            @method('put')

            <div class="card-body">

                <p class="text-danger">* required field</p>

                <div class="row">

                    <div class="col-6">

                        <!--expiration_date-->
                        <div class='form-group'>
                            <label for='datepicker'><span class="text-danger">*</span>Last Date of Payment/Expiration
                                Date</label>
                            <input type='text' name='expiration_date' id='datepicker' class='form-control'
                                value="{{ $due_date_reminder->expiration_date }}" required autocomplete="off">
                        </div>
                        <!--/expiration_date-->

                        <!--notification_date-->
                        <div class='form-group'>
                            <label for='datepicker2'><span class="text-danger">*</span>Notification Date</label>
                            <input type='text' name='notification_date' id='datepicker2' class='form-control'
                                value="{{ $due_date_reminder->notification_date }}" required autocomplete="off">
                        </div>
                        <!--/notification_date-->

                        <!--automatic-->
                        <div class="form-group">
                            <label for="automatic"><span class="text-danger">*</span>Send Automatically?</label>
                            <select class="form-control" id="automatic" name="automatic" required>
                                <option selected>{{ $due_date_reminder->automatic }}</option>
                                <option>yes</option>
                                <option>no</option>
                            </select>
                            @error('automatic')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/automatic-->

                        <!--message-->
                        <div class="form-group">
                            <label for="message">Text Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" aria-describedby="messageHelpBlock"
                                required>{{ $due_date_reminder->message }}</textarea>
                        </div>
                        <div id="messageHelpBlock" class="text-dark">
                            Variables: {{ $event_sms->variables }}
                        </div>
                        <!--/message-->

                    </div>

                </div>

            </div>
            <!--/Card Body-->

            <div class="card-footer">
                <button type="submit" class="btn btn-dark">SAVE</button>
            </div>
            <!--/card-footer-->

        </form>

    </div>
@endsection

@section('pageJs')
    <script>
        $(function() {
            $('#datepicker').datepicker({
                autoclose: !0
            });
        });

        $(function() {
            $('#datepicker2').datepicker({
                autoclose: !0
            });
        });
    </script>
@endsection
