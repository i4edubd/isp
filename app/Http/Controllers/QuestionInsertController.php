<?php

namespace App\Http\Controllers;

use App\Models\question;
use App\Models\question_answer;
use App\Models\question_explanation;
use App\Models\question_option;
use Illuminate\Http\Request;

class QuestionInsertController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @return int
     */
    public static function store()
    {
        $questions = question::all();

        foreach ($questions as $question) {
            $question->delete();
        }

        // << Customer Create
        # 01
        $question = question::create([
            'type' => 'single_select',
            'question' => 'Customer এর secret (username and password) কোথায় থাকবে?',
        ]);
        $options = [
            "Radius Server" => 1,
            "Router" => 0,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'Radius based software use করলে রাউটার এ secret (username and password) রাখা যাবেনা, রাখলে ডিসেবল করে রাখতে হবে।',
        ]);

        # 02
        $question = question::create([
            'type' => 'single_select',
            'question' => 'কোথায় কাস্টমার তৈরি করবেন?',
        ]);
        $options = [
            "Software - এ" => 1,
            "Router - এ" => 0,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'অবশ্যই সফটওয়্যার এ কাস্টমার তৈরি করবেন, রাউটার এ না',
        ]);


        # 03
        $question = question::create([
            'type' => 'single_select',
            'question' => 'software এ কাস্টমার তৈরি করলে কি রাউটারেও কাস্টমার তৈরি হবে?',
        ]);
        $options = [
            "না" => 1,
            "হ্যাঁ" => 0,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'সফটওয়ারে কাস্টমার তৈরি করলে রাউটার এ কাস্টমার তৈরি হবে না। যেহেতু Radius based software এ  কাস্টমার রাউটার এ থাকার দরকার নাই।',
        ]);

        // Customer Create >>


        // << Customer Backup
        # 01
        $question = question::create([
            'type' => 'single_select',
            'question' => 'কাস্টমারের ব্যাকআপ কোথায় রাখা হয়?',
        ]);
        $options = [
            "রাউটার" => 1,
            "সফটওয়্যার" => 0,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'কাস্টমারের ব্যাকআপ রাউটার এ রাখা হয়।',
        ]);

        # 02
        $question = question::create([
            'type' => 'single_select',
            'question' => 'কাস্টমারের ব্যাকআপ রাখাটা কি জরুরি?',
        ]);
        $options = [
            "না" => 0,
            "হ্যাঁ" => 1,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'কোন কারণে সার্ভার crash করলে বা রেডিয়াস এর সাথে আপনার রাউটার communicate করতে না পারলে আপনি রাউটার থেকে কাস্টমারগুলো এনাবেল করে চালাতে পারবেন।',
        ]);

        // Customer Backup>>

        // <<Customer Registration
        # 01
        $question = question::create([
            'type' => 'single_select',
            'question' => 'কাস্টমার কি আমার নেটওয়ার্ক এ নিজে নিজে রেজিস্ট্রেশন করতে পারবে?',
        ]);
        $options = [
            "না" => 0,
            "হ্যাঁ" => 1,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'হটস্পট কাস্টমাররা নিজে নিজে রেজিস্ট্রেশন করতে পারবে।',
        ]);

        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'কাস্টমার প্যানেল থেকে কাস্টমার বিল পরিশোধ করতে পারে এবং পাকেজ ক্রয় করতে পারে।',
        ]);
        $options = [
            "না" => 0,
            "হ্যাঁ" => 1,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'কাস্টমার প্যানেল থেকে কাস্টমার বিল পরিশোধ করতে পারে এবং পাকেজ ক্রয় করতে পারে',
        ]);

        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'Hotspot protocol is suitable for WiFi Only?',
        ]);
        $options = [
            "No" => 1,
            "Yes" => 0,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'You can use hotspot protocol for LAN users also',
        ]);

        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'How much router I can add?',
        ]);
        $options = [
            "Unlimited" => 1,
            "1" => 0,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'You can add routers as you need.',
        ]);

        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'How much customer I can add?',
        ]);
        $options = [
            "Unlimited" => 1,
            "1" => 0,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'You can add customers as you need.',
        ]);


        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'I have ppp customers in my router, to transfer customers from router to software I need to create customer in the software one by one?',
        ]);
        $options = [
            "No" => 1,
            "Yes" => 0,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'You can import customers from router  software with one click only',
        ]);

        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'To use the radius server,  how I will configure the router?',
        ]);
        $options = [
            "I will configure the router manually" => 0,
            "The router will be configured automatically by click on the configure link from the router action menu" => 1,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'When you will click on configure link, the router will be configured automatically',
        ]);


        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'Can I see customers download upload history in the software?',
        ]);
        $options = [
            "No" => 0,
            "Yes" => 1,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'Yes, you can see customers download and upload history in the software',
        ]);

        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'Can I see online customers in the software?',
        ]);
        $options = [
            "No" => 0,
            "Yes" => 1,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'Yes, you can see online customers in the software',
        ]);

        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'Can I collect payments form customers in my personal bKash number?',
        ]);
        $options = [
            "No" => 0,
            "Yes" => 1,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'Yes, you can.',
        ]);

        #
        $question = question::create([
            'type' => 'single_select',
            'question' => 'IPv6 address can give to ppp customers?',
        ]);
        $options = [
            "No" => 0,
            "Yes" => 1,
        ];
        foreach ($options as $key => $value) {
            $question_option = question_option::create([
                'question_id' => $question->id,
                'option' => $key,
            ]);

            if ($value == 1) {
                question_answer::create([
                    'question_id' => $question->id,
                    'question_option_id' => $question_option->id,
                ]);
            }
        }
        question_explanation::create([
            'question_id' => $question->id,
            'explanation' => 'Yes, you can.',
        ]);
    }
}
