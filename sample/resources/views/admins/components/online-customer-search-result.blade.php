<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col">Username</th>
            <th scope="col">MAC Addresses <br> IP Address </th>
            <th scope="col">Download</th>
            <th scope="col">Upload</th>
            <th scope="col">UP Time</th>
            <th scope="col">Updated At</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        <tr id="row-{{ $radacct->id }}">
            <td>
                <a href="#" onclick="showCustomerDetails('{{ $radacct->customer->id }}')">
                    {{ $radacct->username }}
                </a>
            </td>
            <td>{{ $radacct->callingstationid }} <br> {{ $radacct->framedipaddress }} </td>
            <td>{{ $radacct->acctoutputoctets / 1000 / 1000 / 1000 }} GB</td>
            <td>{{ $radacct->acctinputoctets / 1000 / 1000 / 1000 }} GB</td>
            <td>{{ sToHms($radacct->acctsessiontime) }}</td>
            <td>{{ $radacct->acctupdatetime }}</td>
            <td class="d-inline-flex">
                {{-- Live Traffic --}}
                @if ($radacct->customer->connection_type == 'PPPoE')
                    <a class="btn btn-outline-info btn-sm mb-2" href="{{ '#row-' . $radacct->id }}"
                        onclick="monitorTraffic('{{ route('interface-traffic.show', ['radacct' => $radacct->id]) }}')">
                        <i class="fas fa-chart-area"></i>
                        Traffic
                    </a>
                @endif
                {{-- Live Traffic --}}
                {{-- MAC Bind --}}
                @if ($radacct->customer->mac_bind == '0')
                    <a class="btn btn-outline-info btn-sm mb-2" href="{{ '#row-' . $radacct->id }}"
                        id="{{ 'online_customers_mac_bind_' . $radacct->id }}"
                        onclick="callURL('{{ route('mac-bind-create', ['radacct' => $radacct]) }}', '{{ 'online_customers_mac_bind_' . $radacct->id }}')">
                        <i class="fas fa-user-lock"></i> MAC Bind
                    </a>
                @endif
                {{-- MAC Bind --}}
            </td>
        </tr>
    </tbody>
</table>
