<?php

namespace App\Http\Controllers;

use App\Models\question;
use App\Models\question_explanation;
use App\Models\question_option;
use Illuminate\Http\Request;

class QuestionExplanationController extends Controller
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

        return view('admins.developer.question-explanation-create', [
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
            'explanation' => 'required|string',
        ]);

        $question_explanation = new question_explanation();
        $question_explanation->question_id = $question->id;
        $question_explanation->explanation = $request->explanation;
        $question_explanation->save();

        return redirect()->route('questions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_explanation  $question_explanation
     * @return \Illuminate\Http\Response
     */
    public function show(question $question, question_explanation $question_explanation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_explanation  $question_explanation
     * @return \Illuminate\Http\Response
     */
    public function edit(question $question, question_explanation $question_explanation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_explanation  $question_explanation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, question $question, question_explanation $question_explanation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_explanation  $question_explanation
     * @return \Illuminate\Http\Response
     */
    public function destroy(question $question, question_explanation $question_explanation)
    {
        //
    }
}
