<ul class="nav nav-pills mb-2">
    <li class="nav-item">
        <a class="nav-link active"
            href="{{ route('sub_operators.billing_profiles.create', ['operator' => $operator->id]) }}">Edit</a>
    </li>
</ul>

<table id="modal_table" class="table table-bordered" style="width:100%">
    <thead>
        <tr>
            <th scope="col" style="width: 2%">#</th>
            <th scope="col">Billing Type</th>
            <th scope="col">Profile Name</th>
            <th scope="col">Auto Bill</th>
            <th scope="col">Auto Lock</th>
            <th scope="col">Grace Period</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($profiles as $profile )
        <tr>
            <th scope="row">{{ $profile->id }}</th>
            <td>{{ $profile->billing_type }}</td>
            <td>{{ $profile->name }}</td>
            <td>{{ $profile->auto_bill }}</td>
            <td>{{ $profile->auto_lock }}</td>
            <td>{{ $profile->grace_period }} Days</td>
        </tr>
        @endforeach

    </tbody>
</table>