@section('contentTitle')
<h3>Due Date Reminders Preview</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th scope="col">Expiration Date</th>
                    <th scope="col">Notification Date</th>
                    <th scope="col">Automatic</th>
                    <th scope="col">Message</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($reminders as $reminder )
                <tr>
                    <td>{{ $reminder->expiration_date }}</td>
                    <td>{{ $reminder->notification_date }}</td>
                    <td>{{ $reminder->automatic }}</td>
                    <td>{{ $reminder->message }}</td>
                </tr>

                @endforeach

            </tbody>

        </table>

        <div class="row">

            <form method="POST" action="{{ route('generate-due-date-reminders.store') }}">

                @csrf

                <button type="submit" class="btn btn-dark">SAVE</button>

            </form>

            <a class="btn btn-primary ml-4" href="{{ route('due_date_reminders.index') }}" role="button">CANCEL</a>

        </div>
        <!--/row-->

    </div>
    <!--/card-body-->

</div>

@endsection

@section('pageJs')
@endsection