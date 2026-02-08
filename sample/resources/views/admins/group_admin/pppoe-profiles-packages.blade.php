<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col" style="width: 2%">#</th>
            <th scope="col">Connection Type</th>
            <th scope="col">PPP Profile</th>
            <th scope="col">Package Name</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($master_packages as $master_package )
        <tr>
            <th scope="row">{{ $master_package->id }}</th>
            <td>{{ $master_package->connection_type }}</td>
            <td>{{ $master_package->pppoe_profile->name }}</td>
            <td>{{ $master_package->name }}</td>
        </tr>
        @endforeach

    </tbody>
</table>