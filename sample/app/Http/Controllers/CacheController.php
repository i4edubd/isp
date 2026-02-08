<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\all_customer;
use App\Models\backup_setting;
use App\Models\billing_profile;
use App\Models\country;
use App\Models\customer_zone;
use App\Models\device;
use App\Models\disabled_menu;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\mandatory_customers_attribute;
use App\Models\operator;
use App\Models\package;
use App\Models\sms_gateway;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheController extends Controller
{

    /**
     * Retrieving account from the cache
     *
     * @param  \App\Models\operator  $operator
     * @return \App\Models\account
     */
    public static function getResellerAccount(operator $operator)
    {
        $roles = ['operator', 'sub_operator'];

        if (!in_array($operator->role, $roles)) {
            return 0;
        }

        $key = 'app_models_account_' . $operator->id;
        $ttl = 300;
        return Cache::remember($key, $ttl, function () use ($operator) {
            switch ($operator->account_type) {
                case 'debit':
                    $where = [
                        ['account_owner', '=', $operator->id],
                        ['account_provider', '=', $operator->gid],
                    ];
                    return account::where($where)->first();
                    break;

                case 'credit':
                    $where = [
                        ['account_provider', '=', $operator->id],
                        ['account_owner', '=', $operator->gid],
                    ];
                    return account::where($where)->first();
                    break;
            }
        });
    }

    /**
     * Get Customers List Key
     *
     * @param  int $operator_id
     * @return string
     */
    public static function getCustomersListKey(int $operator_id)
    {
        return 'customers_list_' . $operator_id;
    }

    /**
     * Update Customers List Cached @CustomerController::index
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @return void
     */
    public static function updateCustomersList(customer $customer)
    {
        $cache_key = self::getCustomersListKey($customer->operator_id);
        if (Cache::has($cache_key)) {
            try {
                $customers = Cache::get($cache_key);
                $customers->put($customer->id, $customer);
                Cache::put($cache_key, $customers, now()->addMinutes(5));
            } catch (\Throwable $th) {
                Log::channel('debug')->debug($th);
            }
        }
    }

    /**
     * Retrieving Operator From The Cache
     *
     * @param  int $operator_id
     * @return \App\Models\operator
     */
    public static function getOperator(int $operator_id)
    {
        $key = 'app_models_operator_' . $operator_id;

        $ttl = 200;

        return Cache::remember($key, $ttl, function () use ($operator_id) {
            return operator::find($operator_id);
        });
    }

    /**
     * Forget Operator From The Cache
     *
     * @param  int $operator_id
     * @return void
     */
    public static function forgetOperator(int $operator_id)
    {
        $key = 'app_models_operator_' . $operator_id;
        if (Cache::has($key)) {
            Cache::forget($key);
        }
    }

    /**
     * Retrieving Customer From The Cache
     *
     * @param string $mobile
     * @return \App\Models\Freeradius\customer
     */

    public static function getCustomer(string $mobile)
    {

        $customer_mobile = validate_mobile($mobile);

        if ($customer_mobile == 0) {
            abort(500, 'Invalid Mobile Number');
        }

        // Changing cache_key require a change in keys of the forgetCustomer function

        $cache_key = 'customer_' . $customer_mobile;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($customer_mobile) {

            $customer_info = all_customer::where('mobile', $customer_mobile)->firstOrFail();

            $operator = self::getOperator($customer_info->operator_id);

            $model = new customer();

            $model->setConnection($operator->radius_db_connection);

            $customer = $model->findOrFail($customer_info->customer_id);

            return $customer;
        });
    }

    /**
     * Retrieving Customer From The Cache
     *
     * @param \App\Models\all_customer $all_customer
     * @return \App\Models\Freeradius\customer
     */
    public static function getCustomerUseAllCustomer(all_customer $all_customer)
    {
        $cache_key =  'customer_' . $all_customer->operator_id . '_' . $all_customer->customer_id;
        $seconds = 300;
        return Cache::remember($cache_key, $seconds, function () use ($all_customer) {
            $operator = self::getOperator($all_customer->operator_id);
            $model = new customer();
            $model->setConnection($operator->radius_db_connection);
            return $model->findOrFail($all_customer->customer_id);
        });
    }

    /**
     * Removing Customer From The Cache
     *
     * @param  \App\Models\Freeradius\customer
     * @return void
     */
    public static function forgetCustomer(customer $customer)
    {
        $customer_name = strlen($customer->name) ? $customer->name : 'f';

        // keys need to change if change made in the cache retrieving functions
        $keys = [
            'customer_' . $customer->mobile,
            'customer_' . $customer->operator_id . '_' . getVarName($customer->username),
            'customer_' . $customer->gid . '_' . getVarName($customer->username),
            'customer_' . $customer->operator_id . '_' . getVarName($customer_name),
            'customer_' . $customer->gid . '_' . getVarName($customer_name),
            'customer_' . $customer->operator_id . '_' . $customer->id,
            'customer_' . $customer->gid . '_' . $customer->id,
            'radaccts_history_' . $customer->operator_id . '_' . $customer->id,
            'radaccts_history_' . $customer->gid . '_' . $customer->id,
        ];

        foreach ($keys as $key) {
            if (Cache::has($key)) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Retrieving BackupSettings From The Cache
     *
     * @param  int $operator_id
     * @return \App\Models\backup_setting
     */
    public static function getBackupSettings(int $operator_id)
    {
        $key = 'app_models_backup_setting_' . $operator_id;
        $ttl = 200;
        return Cache::remember($key, $ttl, function () use ($operator_id) {
            return backup_setting::where('operator_id', $operator_id)->first();
        });
    }

    /**
     * Retrieving Nas From The Cache
     *
     * @param  int $operator_id
     * @param  int $nas_id
     * @return \App\Models\Freeradius\nas
     */
    public static function getNas(int $operator_id, int $nas_id)
    {
        $key = 'app_models_freeradius_nas_' . $operator_id . '_' . $nas_id;
        $ttl = 200;
        $operator = self::getOperator($operator_id);
        return Cache::remember($key, $ttl, function () use ($operator, $nas_id) {
            $model = new nas();
            $model->setConnection($operator->node_connection);
            return $model->find($nas_id);
        });
    }

    /**
     * Retrieving Package From The Cache
     *
     * @param  int $package_id
     * @return \App\Models\package
     */
    public static function getPackage(int $package_id)
    {
        $key = 'app_models_package_' . $package_id;
        $ttl = 200;
        return Cache::remember($key, $ttl, function () use ($package_id) {
            return package::with(['parent_package', 'master_package'])->find($package_id);
        });
    }

    /**
     * Retrieving country From The Cache
     *
     * @param  int $country_id
     * @return \App\Models\country
     */
    public static function getCountry(int $country_id)
    {
        $key = 'app_models_country_' . $country_id;
        $ttl = 600;
        return Cache::remember($key, $ttl, function () use ($country_id) {
            return country::find($country_id);
        });
    }

    /**
     * Retrieving sms_gateway From The Cache
     *
     * @param  int $sms_gateway_id
     * @return \App\Models\sms_gateway
     */
    public static function getSmsGateway(int $sms_gateway_id): sms_gateway
    {
        $key = 'app_models_sms_gateway_' . $sms_gateway_id;
        $ttl = 600;
        return Cache::remember($key, $ttl, function () use ($sms_gateway_id) {
            return sms_gateway::find($sms_gateway_id);
        });
    }

    /**
     * Retrieving Billing Profile From The Cache
     *
     * @param  int $package_id
     * @return \App\Models\package
     */
    public static function getBillingProfile($id)
    {
        $cache_key = 'billing_profile_' . $id;
        $seconds = 200;
        return Cache::remember($cache_key, $seconds, function () use ($id) {
            return billing_profile::find($id);
        });
    }

    /**
     * Retrieving customer_zone From The Cache
     *
     * @param  int $package_id
     * @return \App\Models\package
     */
    public static function getZone($id)
    {
        $cache_key = 'customer_zone_' . $id;
        $seconds = 600;
        return Cache::remember($cache_key, $seconds, function () use ($id) {
            return customer_zone::find($id);
        });
    }

    /**
     * Retrieving device From The Cache
     *
     * @param  int $package_id
     * @return \App\Models\package
     */
    public static function getDevice($id)
    {
        $cache_key = 'device_' . $id;
        $seconds = 600;
        return Cache::remember($cache_key, $seconds, function () use ($id) {
            return device::find($id);
        });
    }

    /**
     * Get Disabled Menus
     *
     * @param  \App\Models\operator $operator
     * @return \Illuminate\Support\Collection
     */
    public static function getDisabledMenus(operator $operator)
    {
        $cache_key = 'app_models_disabled_menus_' . $operator->id;
        $seconds = 600;
        return Cache::remember($cache_key, $seconds, function () use ($operator) {
            return disabled_menu::where('operator_id', $operator->id)->get();
        });
    }

    /**
     * Get Mandatory Customer Attribute
     *
     * @param  \App\Models\operator $operator
     * @return \Illuminate\Support\Collection
     */
    public static function getMandatoryCustomerAttribute(operator $operator)
    {
        $cache_key = 'app_models_mandatory_customers_attribute_' . $operator->mgid;
        $seconds = 600;
        return Cache::remember($cache_key, $seconds, function () use ($operator) {
            return mandatory_customers_attribute::where('mgid', $operator->mgid)->get();
        });
    }

    /**
     * Removing Disabled Menus From The Cache
     *
     * @param  \App\Models\operator $operator
     * @return void
     */
    public static function forgetDisabledMenus(operator $operator)
    {
        $cache_key = 'app_models_disabled_menus_' . $operator->id;
        if (Cache::has($cache_key)) {
            Cache::forget($cache_key);
        }
    }
}
