<?php

namespace App\Console\Commands;

use App\Http\Controllers\enum\TelegramChatsType;
use App\Models\operator;
use App\Models\telegraph_bot;
use App\Models\telegraph_chat;
use Illuminate\Console\Command;

class TelegramChatAddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:add_chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Telegram Chat';

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

        $bots = telegraph_bot::all(['id', 'operator_id', 'name'])->toArray();
        $this->table(['id', 'operator_id', 'name'], $bots);

        $telegraph_bot_id = $this->ask('telegraph_bot_id ?');
        $chat_id = $this->ask('chat_id ?');

        $names = TelegramChatsType::cases();
        foreach ($names as $key => $value) {
            $this->info("$key >> $value->name");
        }
        $name = $this->ask('name?');

        $telegraph_chat = new telegraph_chat();
        $telegraph_chat->operator_id = $operator_id;
        $telegraph_chat->chat_id = $chat_id;
        $telegraph_chat->name = $name;
        $telegraph_chat->telegraph_bot_id = $telegraph_bot_id;
        $telegraph_chat->save();

        $this->info('Chat added successfully.');

        return Command::SUCCESS;
    }
}
