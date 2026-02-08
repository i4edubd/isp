@extends ('laraview.layouts.sideNavLayout')

@section('title')
Question Option Create
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '11';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@section('contentTitle')
<h3>Question Option Create</h3>
@endsection

@section('content')

<div class="card">

    <form id="quickForm" method="POST"
        action="{{ route('questions.question_options.store', ['question' => $question->id]) }}">

        @csrf

        <div class="card-body">

            <p> Question: {{ $question->question }} </p>

            <hr>

            <p> Options: </p>
            @foreach ($options as $option)
            <div class="form-check">
                <input class="form-check-input" type="radio" id="{{ $option->id }}" value="{{ $option->id }}">
                <label class="form-check-label" for="{{ $option->id }}">
                    {{ $option->option }}
                </label>
            </div>
            @endforeach

            <hr>

            <!--option-->
            <div class="form-group">
                <label for="option"><span class="text-danger">*</span>option</label>
                <input name="option" type="text" class="form-control @error('option') is-invalid @enderror" id="option"
                    value="{{ old('option') }}" required>
            </div>
            <!--/option-->

            <!--Add another-->
            <div class="form-group">
                <label for="another"><span class="text-danger">*</span>Add Another</label>
                <select class="form-control" id="another" name="another" required>
                    <option value="yes">yes</option>
                    <option value="no">no</option>
                </select>
            </div>
            <!--/another-->

        </div>
        <!--/card-body-->

        <div class="card-footer">
            <button type="submit" class="btn btn-dark">Submit</button>
        </div>
        <!--/card-footer-->

    </form>

</div>

@endsection

@section('pageJs')
@endsection
