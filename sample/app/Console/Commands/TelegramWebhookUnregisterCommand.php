<?php

namespace App\Console\Commands;

use App\Models\telegraph_bot;
use Illuminate\Console\Command;

class TelegramWebhookUnregisterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:unregister_webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unregistering Webhooks';

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

        $bots = telegraph_bot::all(['id', 'operator_id', 'name'])->toArray();
        $this->table(['id', 'operator_id', 'name'], $bots);

        $telegraph_bot_id = $this->ask('telegraph_bot_id ?');

        $bot = telegraph_bot::findOrFail($telegraph_bot_id);
        $response = $bot->unregisterWebhook(true)->send();

        $this->info($response);

        return Command::SUCCESS;
    }
}
