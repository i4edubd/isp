<?php

namespace App\Http\Controllers;

use App\Models\question;
use App\Models\question_answer;
use App\Models\question_option;
use Illuminate\Http\Request;

class QuestionAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\question  $question
     * @return \Illuminate\Http\Response
     */
    public function index(question $question)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\question  $question
     * @return \Illuminate\Http\Response
     */
    public function create(question $question)
    {
        $options = question_option::where('question_id', $question->id)->get();

        return view('admins.developer.question-answer-create', [
            'options' => $options,
            'question' => $question,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\question  $question
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, question $question)
    {
        $request->validate([
            'question_option_id' => 'required|numeric',
        ]);

        $question_answer = new question_answer();
        $question_answer->question_id = $question->id;
        $question_answer->question_option_id = $request->question_option_id;
        $question_answer->save();

        return redirect()->route('questions.question_explanations.create', ['question' => $question->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_answer  $question_answer
     * @return \Illuminate\Http\Response
     */
    public function show(question $question, question_answer $question_answer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_answer  $question_answer
     * @return \Illuminate\Http\Response
     */
    public function edit(question $question, question_answer $question_answer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_answer  $question_answer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, question $question, question_answer $question_answer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_answer  $question_answer
     * @return \Illuminate\Http\Response
     */
    public function destroy(question $question, question_answer $question_answer)
    {
        //
    }
}
