<?php

namespace App\Console\Commands;

use App\Models\telegraph_chat;
use Illuminate\Console\Command;

class TelegramSendHelloCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:send_hello';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Hellow Message';

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

        $chats = telegraph_chat::all(['id', 'operator_id', 'telegraph_bot_id', 'chat_id', 'name'])->toArray();
        $this->table(['id', 'operator_id', 'telegraph_bot_id', 'chat_id', 'name'], $chats);

        $id = $this->ask('chat id ?');

        $chat = telegraph_chat::findOrFail($id);

        $response = $chat->html('Hello')->send();

        $this->info($response);

        return Command::SUCCESS;
    }
}
