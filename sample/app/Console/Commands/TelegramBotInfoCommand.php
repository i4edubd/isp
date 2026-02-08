<?php

namespace App\Console\Commands;

use App\Models\telegraph_bot;
use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Console\Command;

class TelegramBotInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:bot_info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves the bot data from Telegram APIs';

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

        $response = Telegraph::bot($bot)->botInfo()->send();

        $this->info($response);

        return Command::SUCCESS;
    }
}
