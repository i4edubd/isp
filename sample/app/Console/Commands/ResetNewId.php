<?php

namespace App\Console\Commands;

use App\Models\billing_profile;
use App\Models\billing_profile_operator;
use App\Models\card_distributor;
use App\Models\card_distributor_payments;
use App\Models\cash_in;
use App\Models\cash_out;
use App\Models\complain_category;
use App\Models\custom_field;
use App\Models\custom_price;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\customer_zone;
use App\Models\department;
use App\Models\device;
use App\Models\due_date_reminder;
use App\Models\expense;
use App\Models\expense_category;
use App\Models\expense_subcategory;
use App\Models\fair_usage_policy;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\customer_custom_attribute;
use App\Models\Freeradius\nas;
use App\Models\ipv4pool;
use App\Models\ipv6pool;
use App\Models\master_package;
use App\Models\operator;
use App\Models\operator_permission;
use App\Models\package;
use App\Models\pppoe_profile;
use App\Models\recharge_card;
use App\Models\sms_gateway;
use Illuminate\Console\Command;

class ResetNewId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset_new_id {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $mgid = $this->argument('mgid');

        // billing_profiles
        if (config('local.host_type') == 'central') {
            $rows = billing_profile::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // billing_profile_operator
        if (config('local.host_type') == 'central') {
            $rows = billing_profile_operator::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // card_distributors
        if (config('local.host_type') == 'central') {
            $rows = card_distributor::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // card_distributor_payments
        if (config('local.host_type') == 'central') {
            $rows = card_distributor_payments::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // cash_ins
        // $rows = cash_in::all();
        // while ($row = $rows->shift()) {
        //     $row->new_id = 0;
        //     $row->save();
        // }

        // cash_outs
        // $rows = cash_out::all();
        // while ($row = $rows->shift()) {
        //     $row->new_id = 0;
        //     $row->save();
        // }

        // complain_categories
        if (config('local.host_type') == 'central') {
            $rows = complain_category::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // customers
        $rows = customer::where('mgid', $mgid)->get();
        while ($row = $rows->shift()) {
            $row->new_id = 0;
            $row->save();
        }

        // customer_bills
        if (config('local.host_type') == 'central') {
            $rows = customer_bill::where('mgid', $mgid)->get();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // customer_custom_attributes
        $rows = customer_custom_attribute::all();
        while ($row = $rows->shift()) {
            $row->new_id = 0;
            $row->save();
        }

        // customer_payments
        if (config('local.host_type') == 'central') {
            $rows = customer_payment::where('mgid', $mgid)->get();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // customer_zones
        if (config('local.host_type') == 'central') {
            $rows = customer_zone::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // custom_fields
        if (config('local.host_type') == 'central') {
            $rows = custom_field::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // custom_prices
        if (config('local.host_type') == 'central') {
            $rows = custom_price::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // departments
        if (config('local.host_type') == 'central') {
            $rows = department::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // devices
        if (config('local.host_type') == 'central') {
            $rows = device::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // due_date_reminders
        if (config('local.host_type') == 'central') {
            $rows = due_date_reminder::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // expenses
        // $rows = expense::all();
        // while ($row = $rows->shift()) {
        //     $row->new_id = 0;
        //     $row->save();
        // }

        // expense_categories
        if (config('local.host_type') == 'central') {
            $rows = expense_category::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // expense_subcategories
        if (config('local.host_type') == 'central') {
            $rows = expense_subcategory::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // fair_usage_policies
        if (config('local.host_type') == 'central') {
            $rows = fair_usage_policy::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // ipv4pools
        if (config('local.host_type') == 'central') {
            $rows = ipv4pool::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // ipv6pools
        if (config('local.host_type') == 'central') {
            $rows = ipv6pool::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // master_packages
        if (config('local.host_type') == 'central') {
            $rows = master_package::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // nas
        $rows = nas::all();
        while ($row = $rows->shift()) {
            $row->new_id = 0;
            $row->save();
        }

        // operators
        if (config('local.host_type') == 'central') {
            $rows = operator::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // operator_permissions
        if (config('local.host_type') == 'central') {
            $rows = operator_permission::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // packages
        if (config('local.host_type') == 'central') {
            $rows = package::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // pppoe_profiles
        if (config('local.host_type') == 'central') {
            $rows = pppoe_profile::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        // recharge_cards
        // $rows = recharge_card::all();
        // while ($row = $rows->shift()) {
        //     $row->new_id = 0;
        //     $row->save();
        // }

        // sms_gateways
        if (config('local.host_type') == 'central') {
            $rows = sms_gateway::all();
            while ($row = $rows->shift()) {
                $row->new_id = 0;
                $row->save();
            }
        }

        return 0;
    }
}
