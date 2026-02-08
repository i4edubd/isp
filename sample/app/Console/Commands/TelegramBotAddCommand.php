<?php

namespace App\Console\Commands;

use App\Models\operator;
use App\Models\telegraph_bot;
use Illuminate\Console\Command;

class TelegramBotAddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:add_bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Telegram Bot';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('local.host_type') != 'central') {
            $this->info('Need a central host to execute the command.');
            return false;
        }

        $operator_id = $this->ask('operator_id ?');
        $operator = operator::findOrFail($operator_id);

        // confirm operator
        $info = "Name : $operator->name >> Email: $operator->email >> role: $operator->role >> company: $operator->company";
        $this->info($info);
        if ($this->confirm('Do you wish to continue?') == false) {
            $this->info('Try Again');
            return false;
        }

        $token = $this->ask('token ?');
        $name = $this->ask('Bot Name ?');

        $telegraph_bot = new telegraph_bot();
        $telegraph_bot->operator_id = $operator_id;
        $telegraph_bot->token = $token;
        $telegraph_bot->name = $name;
        $telegraph_bot->save();

        $this->info('Bot added successfully. Id is: ' . $telegraph_bot->id);

        return Command::SUCCESS;
    }
}
