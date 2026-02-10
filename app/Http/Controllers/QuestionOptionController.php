<?php

namespace App\Http\Controllers;

use App\Models\question;
use App\Models\question_option;
use Illuminate\Http\Request;

class QuestionOptionController extends Controller
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

        return view('admins.developer.question-options-create', [
            'question' => $question,
            'options' => $options,
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
            'option' => 'required',
            'another' => 'required',
        ]);

        $question_option = new question_option();
        $question_option->question_id = $question->id;
        $question_option->option = $request->option;
        $question_option->save();

        if ($request->another == 'yes') {
            return redirect()->route('questions.question_options.create', ['question' => $question->id]);
        }

        return redirect()->route('questions.question_answers.create', ['question' => $question->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_option  $question_option
     * @return \Illuminate\Http\Response
     */
    public function show(question $question, question_option $question_option)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_option  $question_option
     * @return \Illuminate\Http\Response
     */
    public function edit(question $question, question_option $question_option)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_option  $question_option
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, question $question, question_option $question_option)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\question  $question
     * @param  \App\Models\question_option  $question_option
     * @return \Illuminate\Http\Response
     */
    public function destroy(question $question, question_option $question_option)
    {
        //
    }
}
