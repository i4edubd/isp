<?php

namespace App\Console\Commands;

use App\Http\Controllers\QuestionInsertController;
use Illuminate\Console\Command;

class InsertQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert Exam Questions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        QuestionInsertController::store();
        return 0;
    }
}
