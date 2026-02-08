<?php

namespace App\Console\Commands;

use App\Models\card_distributor;
use App\Models\operator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InternationalAttributesMonitoringCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:international_attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor International Attributes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if (config('local.host_type') != 'central') {
            return Command::SUCCESS;
        }

        try {
            // operators
            $null_country_id = operator::whereNull('country_id')->count();
            $null_timezone = operator::whereNull('timezone')->count();
            $null_lang_code = operator::whereNull('lang_code')->count();

            if ($null_country_id) {
                Log::channel('international_attributes')->info('Null country_id count @operators :' . $null_country_id);
            }
            if ($null_timezone) {
                Log::channel('international_attributes')->info('Null timezone count @operators :' . $null_timezone);
            }
            if ($null_lang_code) {
                Log::channel('international_attributes')->info('Null lang_code count @operators :' . $null_lang_code);
            }

            // card_distributors
            $null_country_id = card_distributor::whereNull('country_id')->count();
            $null_timezone = card_distributor::whereNull('timezone')->count();
            $null_lang_code = card_distributor::whereNull('lang_code')->count();

            if ($null_country_id) {
                Log::channel('international_attributes')->info('Null country_id count @card_distributors :' . $null_country_id);
            }
            if ($null_timezone) {
                Log::channel('international_attributes')->info('Null timezone count @card_distributors :' . $null_timezone);
            }
            if ($null_lang_code) {
                Log::channel('international_attributes')->info('Null lang_code count @card_distributors :' . $null_lang_code);
            }
        } catch (\Throwable $th) {
            Log::channel('international_attributes')->error($th->getTraceAsString());
        }

        return Command::SUCCESS;
    }
}
