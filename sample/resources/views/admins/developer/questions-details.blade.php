<p> Question: {{ $question->question }} </p>

<p> Options: </p>
@foreach ($question->options as $option)
<div class="form-check">
    <input class="form-check-input" type="radio" id="{{ $option->id }}" value="{{ $option->id }}">
    <label class="form-check-label" for="{{ $option->id }}">
        {{ $option->option }}
    </label>
</div>
@endforeach

<hr>

<p> Answer: {{ $question->answer->question_option->option }}</p>

<p> Explanation: {{ $question->explanation->explanation }}</p>
