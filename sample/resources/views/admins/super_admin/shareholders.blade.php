<table id="data_table" class="table table-bordered table-striped" style="width:100%;">
    <thead>
        <tr>
            <th>Operator</th>
            <th>Role</th>
            <th>Share</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($shareholders as $shareholder)
        <tr>
            <td>{{ $shareholder->operator->name }}</td>
            <td>{{ $shareholder->operator->role }}</td>
            <td>{{ $shareholder->share }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
