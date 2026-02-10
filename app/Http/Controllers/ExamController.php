<?php

namespace App\Http\Controllers;

use App\Models\question;
use App\Models\question_option;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'show' => 'required|numeric',
        ]);

        $all_questions = question::all();

        $total_count = $all_questions->count();

        if ($total_count == 0) {
            return view('admins.group_admin.no-questions-found');
        }

        if ($total_count == $request->show) {
            $operator = $request->user();
            $operator->exam_attendance = 1;
            $operator->save();
            if (MinimumConfigurationController::hasPendingConfig($operator)) {
                return redirect()->route('configuration.next', ['operator' => $operator->id]);
            } else {
                return view('admins.group_admin.examp-thanks');
            }
        }

        $array = $all_questions->toArray();

        $question = $array[$request->show];

        $options = question_option::where('question_id', $question['id'])->get();

        $show = $request->show + 1;

        return view('admins.group_admin.exam-index', [
            'question' => $question,
            'options' => $options,
            'show' => $show,
            'total' => $total_count,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|numeric',
            'show' => 'required|numeric',
            'question_option_id' => 'required|numeric',
        ]);

        return redirect()->route('exam.index', ['show' => $request->show]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(question $question)
    {

        return view('admins.group_admin.question-answer', [
            'question' => $question,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(question $question)
    {
        //
    }
}
