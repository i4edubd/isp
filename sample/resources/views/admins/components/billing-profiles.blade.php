@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered">

            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Billing Type</th>
                    <th scope="col">Profile Name</th>
                    <th scope="col">Auto Bill</th>
                    <th scope="col">Auto Suspend</th>
                    <th scope="col">Grace Period</th>
                    <th scope="col"></th>
                </tr>
            </thead>

            <tbody>

                @foreach ($profiles->sortBy('billing_type') as $profile )

                <tr>
                    <th scope="row">{{ $profile->id }}</th>
                    <td>{{ $profile->billing_type }}</td>
                    <td>{{ $profile->name }}</td>
                    <td>{{ $profile->auto_bill }}</td>
                    <td>{{ $profile->auto_lock }}</td>
                    <td>{{ $profile->grace_period }} Days</td>
                    <td>

                        {{-- Actions --}}
                        @if (Auth::user()->role == 'group_admin')

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                {{-- Edit --}}
                                @can('update', $profile)
                                <a class="dropdown-item"
                                    href="{{ route('billing_profiles.edit', ['billing_profile' => $profile->id]) }}">
                                    Edit
                                </a>
                                @endcan
                                {{-- Edit --}}

                                {{-- Delete --}}
                                @can('delete', $profile)
                                <form method="post"
                                    action="{{ route('billing_profiles.destroy',['billing_profile' => $profile->id]) }}"
                                    onsubmit="return confirm('Are you sure to Delete')">
                                    @csrf
                                    @method('delete')
                                    <button class="dropdown-item" type="submit">Delete</button>
                                </form>
                                @endcan
                                {{-- Delete --}}

                                {{-- Replace --}}
                                @can('replace', $profile)
                                <a class="dropdown-item"
                                    href="{{ route('billing_profile_replace.edit', ['billing_profile' => $profile->id]) }}">
                                    Replace
                                </a>
                                @endcan
                                {{-- Replace --}}

                                {{-- Customers --}}
                                <a class="dropdown-item"
                                    href="{{ route('customers.index', ['billing_profile_id' => $profile->id]) }}">
                                    Customers
                                </a>
                                {{-- Customers --}}

                            </div>

                        </div>

                        @endif
                        {{-- Actions --}}

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection

@section('pageJs')
@endsection