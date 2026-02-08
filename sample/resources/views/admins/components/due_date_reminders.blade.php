@section('contentTitle')
    <h3>Due Date Reminders</h3>
@endsection

@section('content')
    <div class="card">

        <ul class="nav flex-column flex-sm-row">

            <!--New Reminder-->
            <li class="nav-item ml-2">
                <a class="btn btn-outline-primary my-2 my-sm-0" href="{{ route('due-notifier.create') }}">
                    <i class="fas fa-bolt"></i>
                    Instant Notification
                </a>
            </li>
            <!--/New Reminder-->

            <!--New Reminder-->
            <li class="nav-item ml-2">
                <a class="btn btn-outline-danger my-2 my-sm-0" href="{{ route('due_date_reminders.create') }}">
                    <i class="fas fa-plus"></i>
                    Create New Schedule
                </a>
            </li>
            <!--/New Reminder-->

            <!--generate-defaults-->
            <li class="nav-item ml-2">
                <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('generate-due-date-reminders.create') }}">
                    <i class="fas fa-plus"></i>
                    Generate Defaults
                </a>
            </li>
            <!--/generate-defaults-->

        </ul>

        <h5 class="card-header">Notification Schedules</h5>

        <div class="card-body">

            <table id="data_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Expiration Date</th>
                        <th scope="col">Notification Date</th>
                        <th scope="col">Automatic</th>
                        <th scope="col">Message</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($reminders->sortBy('expiration_date') as $reminder)
                        <tr>
                            <th scope="row">{{ $reminder->id }}</th>
                            <td>{{ $reminder->expiration_date }}</td>
                            <td>{{ $reminder->notification_date }}</td>
                            <td>{{ $reminder->automatic }}</td>
                            <td>{{ $reminder->message }}</td>
                            <td>

                                <div class="btn-group" role="group">

                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>

                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                        <a class="dropdown-item"
                                            href="{{ route('due_date_reminders.notification.create', ['due_date_reminder' => $reminder->id]) }}">
                                            Notify Now
                                        </a>

                                        <a class="dropdown-item"
                                            href="{{ route('due_date_reminders.edit', ['due_date_reminder' => $reminder->id]) }}">
                                            Edit
                                        </a>

                                        <form method="post"
                                            action="{{ route('due_date_reminders.destroy', ['due_date_reminder' => $reminder->id]) }}"
                                            onsubmit="return confirm('Are you sure to Delete')">
                                            @csrf
                                            @method('delete')
                                            <button class="dropdown-item" type="submit">Delete</button>
                                        </form>

                                    </div>

                                </div>

                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>

        <!--/card-body-->

    </div>
@endsection

@section('pageJs')
@endsection
