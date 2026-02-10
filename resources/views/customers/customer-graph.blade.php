@extends('layouts.metronic_demo1')

@section('title')
    Bandwidth Graph
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('contentTitle')
    @include('customers.logout-nav')
@endsection

@section('content')
<div class="card card-flush">

    {{-- Navigation bar --}}
    <div class="card-header">
        @php
            $active_link = '7';
        @endphp
        @include('customers.nav-links')
    </div>
    {{-- Navigation bar --}}

    <div id="live_traffic"></div>

    <div class="card-body">
        <h5 class="text-sm font-semibold mb-4">Hourly Graph</h5>
        <div class="overflow-auto">
            <img class="max-w-full h-auto" src="{{ $graph->get('hourly') }}" alt="Hourly Graph">
        </div>
    </div>

    <div class="card-body border-t">
        <h5 class="text-sm font-semibold mb-4">Daily Graph</h5>
        <div class="overflow-auto">
            <img class="max-w-full h-auto" src="{{ $graph->get('daily') }}" alt="Daily Graph">
        </div>
    </div>

    <div class="card-body border-t">
        <h5 class="text-sm font-semibold mb-4">Weekly Graph</h5>
        <div class="overflow-auto">
            <img class="max-w-full h-auto" src="{{ $graph->get('weekly') }}" alt="Weekly Graph">
        </div>
    </div>

    <div class="card-body border-t">
        <h5 class="text-sm font-semibold mb-4">Monthly Graph</h5>
        <div class="overflow-auto">
            <img class="max-w-full h-auto" src="{{ $graph->get('monthly') }}" alt="Monthly Graph">
        </div>
    </div>

    @include('customers.footer-nav-links')

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let liveUrl = "{{ route('customers.live_traffic') }}";
        let intervalId = setInterval(function() {
            window.axios.get(liveUrl)
                .then(response => {
                    if (response.data === "0") {
                        clearInterval(intervalId);
                    } else {
                        document.getElementById('live_traffic').innerHTML = response.data;
                    }
                })
                .catch(() => clearInterval(intervalId));
        }, 3000);
    });
</script>
@endpush

@endsection
