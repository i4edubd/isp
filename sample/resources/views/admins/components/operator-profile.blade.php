@section('contentTitle')
    <h4>Biller</h4>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <ul class="list-group">

                <li class="list-group-item"><span class="font-weight-bold">Company Logo: </span>
                    <img src="/storage/{{ $operator->company_logo }}" alt="Logo">
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Company Name: </span>
                    {{ $operator->company }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">
                        Company Name in {{ getLanguage(Auth::user())->name }}:
                    </span>
                    {{ $operator->company_in_native_lang }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Mobile: </span>
                    {{ $operator->mobile }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Helpline: </span>
                    {{ $operator->helpline }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Email: </span>
                    {{ $operator->email }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Country: </span>
                    {{ $operator->country->name }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">District: </span>
                    {{ $operator->district }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Street Name/Number: </span>
                    {{ $operator->road_no }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">House Number: </span>
                    {{ $operator->house_no }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Postal Code: </span>
                    {{ $operator->postal_code }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Language: </span>
                    {{ getLanguage(Auth::user())->name }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Time zone: </span>
                    {{ Auth::user()->timezone }}
                </li>

            </ul>

        </div>

        <div class="card-footer">

            <a href="{{ route('operators.profile.create', ['operator' => $operator->id]) }}" class="card-link">
                EDIT
            </a>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
