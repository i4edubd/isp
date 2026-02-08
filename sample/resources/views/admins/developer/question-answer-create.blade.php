@extends ('laraview.layouts.sideNavLayout')

@section('title')
Question Answer Create
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
<h3>Question Answer</h3>
@endsection

@section('content')

<div class="card">

    <form id="quickForm" method="POST"
        action="{{ route('questions.question_answers.store', ['question' => $question->id]) }}">

        @csrf

        <div class="card-body">

            <p> Question: {{ $question->question }} </p>

            @foreach ($options as $option)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="question_option_id" id="{{ $option->id }}"
                    value="{{ $option->id }}">
                <label class="form-check-label" for="{{ $option->id }}">
                    {{ $option->option }}
                </label>
            </div>
            @endforeach

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
