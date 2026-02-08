@extends ('laraview.layouts.topNavLayout')

@section('title')
    Complaints
@endsection

@section('pageCss')
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('topNavbar')
@endsection

@section('contentTitle')
    @include('customers.logout-nav')
@endsection

@section('content')
    <div class="card">

        {{-- Navigation bar --}}
        <div class="card-header">
            @php
                $active_link = '6';
            @endphp
            @include('customers.nav-links')
        </div>
        {{-- Navigation bar --}}

        {{-- New Complaint --}}
        <ul class="nav justify-content-end">

            <li class="nav-item">
                <a class="nav-link text-danger" href="{{ route('complaints-customer-interface.create') }}">
                    <i class="fas fa-plus"></i>
                    New Complaint
                </a>
            </li>

        </ul>
        {{-- New Complaint --}}

        <div class="card-body">

            <table id="data_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Category</th>
                        <th scope="col">Complaint</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->category->name }}</td>
                            <td>{{ $complaint->message }}</td>
                            <td>{{ $complaint->status }}</td>
                            <td>
                                <a
                                    href="{{ route('complaints-customer-interface.show', ['customer_complain' => $complaint]) }}">
                                    <i class="fas fa-exchange-alt"></i>
                                    Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        @include('customers.footer-nav-links')

    </div>
@endsection

@section('pageJs')
@endsection
