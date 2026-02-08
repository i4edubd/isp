@extends ('laraview.layouts.sideNavLayout')

@section('title')
Questions & Answers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '12';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
<h3>Questions & Answers</h3>
@endsection

@section('content')

<div class="card">

    <form id="quickForm" method="POST"
        action="{{ route('exam.store', ['question' => $question['id'], 'show' => $show]) }}">

        @csrf

        <div class="card-body">

            <p> Question ({{ $show }} of {{ $total }}): {{ $question['question'] }} </p>

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

            <a class="btn btn-link ml-4" href="#" role="button"
                onclick="showAnswer('{{ route('exam.show', ['question' => $question['id']]) }}')">Show Answer</a>

            <div id="answer"> </div>

        </div>
        <!--/card-footer-->

    </form>

</div>

@endsection

@section('pageJs')

<script>
    function showAnswer(url)
    {
        $.get( url, function( data ) {
            $("#answer").html(data);
        });
    }
</script>

@endsection
