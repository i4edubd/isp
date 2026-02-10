<?php

namespace App\Models\Freeradius;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\enum\CustomerOverallStatus;
use App\Http\Controllers\enum\CustomerType;
use App\Models\customer_change_log;
use App\Models\customer_payment;
use App\Models\fair_usage_policy;
use App\Models\package;
use App\Models\sms_history;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class customer extends Model
{
    use HasFactory;

    /**
     * The model type
     *
     * @var string|null (node|central)
     */
    protected $modelType = 'node';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['address', 'billing_profile', 'color', 'device', 'is_online', 'last_seen', 'overall_status', 'payments', 'payment_color', 'role', 'remaining_validity', 'sms_histories', 'type', 'zone'];

    /**
     * Set connection for Node Model
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        if (Auth::user()) {

            $operator = Auth::user();

            $this->connection = $operator->radius_db_connection;
        }

        parent::__construct($attributes);
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Customer Billing Profile.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function address(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                $address =  $attributes['name'] . "<br>"
                    . "Mobile: " . $attributes['mobile'] . "<br>";

                if (strlen($attributes['house_no'])) {
                    $address .= "H# " . $attributes['house_no'] . "<br>";
                }

                if (strlen($attributes['road_no'])) {
                    $address .= "R# " . $attributes['road_no'] . "<br>";
                }

                if (strlen($attributes['thana'])) {
                    $address .=  $attributes['thana'] . "," . $attributes['district'];
                }

                return $address;
            },
        )->shouldCache();
    }

    /**
     * Customer Billing Profile.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function billingProfile(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                $billing_profile_id = $attributes['billing_profile_id'];

                if (!$billing_profile_id) {
                    return 'N/A';
                }

                $billing_profile = CacheController::getBillingProfile($billing_profile_id);

                if ($billing_profile) {
                    return $billing_profile->name;
                } else {
                    return 'N/A';
                }
            },
        );
    }

    /**
     * Get color attribute
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function color(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                $color = "text-dark";

                if ($attributes['status'] == 'suspended' || $attributes['status'] == 'fup') {
                    $color = "text-warning";
                }

                if ($attributes['status'] == 'disabled') {
                    $color = "text-danger";
                }

                return $color;
            },
        );
    }

    /**
     * Get the child accounts for the customer.
     */
    public function childAccounts()
    {
        return $this->hasMany(customer::class, 'parent_id', 'id');
    }

    /**
     * Get the custom attributes for the customer.
     */
    public function custom_attributes()
    {
        return $this->hasMany(customer_custom_attribute::class);
    }

    /**
     * Get the custom attributes for the customer.
     */
    public function customer_change_logs()
    {
        return $this->hasMany(customer_change_log::class);
    }

    /**
     * Customer device Name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function device(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['device_id'] < 1) {
                    return null;
                }
                $device = CacheController::getDevice($attributes['device_id']);
                if ($device) {
                    return $device->name;
                } else {
                    return null;
                }
            },
        );
    }

    /**
     * Get the online status for the customer.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function isOnline(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (is_null($value)) {
                    return radacct::where('username', $attributes['username'])->whereNull('acctstoptime')->count();
                } else {
                    return $value;
                }
            },
        );
    }

    /**
     * Get Last Seen
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function lastSeen(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['last_seen_timestamp'] == 0) {
                    return 'Never';
                }

                return Carbon::createFromTimestamp($attributes['last_seen_timestamp'])->diffForHumans(Carbon::now());
            },
        );
    }

    /**
     * Get overall Status
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function overallStatus(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {
                switch ($attributes['payment_status']) {
                    case 'paid':
                        return match ($attributes['status']) {
                            'active' => CustomerOverallStatus::PAID_ACTIVE->name,
                            'suspended' => CustomerOverallStatus::PAID_SUSPENDED->name,
                            'disabled' => CustomerOverallStatus::PAID_DISABLED->name,
                        };
                        break;

                    case 'billed':
                        return match ($attributes['status']) {
                            'active' => CustomerOverallStatus::BILLED_ACTIVE->name,
                            'suspended' => CustomerOverallStatus::BILLED_SUSPENDED->name,
                            'disabled' => CustomerOverallStatus::BILLED_DISABLED->name,
                        };
                        break;
                }
            },
        );
    }

    /**
     * Get the parent for the customer.
     *
     * @return \App\Models\Freeradius\customer
     */
    public function parent()
    {
        return $this->belongsTo(customer::class, 'parent_id', 'id')->withDefault();
    }

    /**
     * Get the payments for the customer.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function payments(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                $where = [
                    ['operator_id', '=', $attributes['operator_id']],
                    ['customer_id', '=', $attributes['id']],
                ];

                return customer_payment::where($where)->get();
            },
        );
    }

    /**
     * Get the payments for the customer.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function paymentColor(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['payment_status'] == 'billed') {
                    return "text-warning";
                } else {
                    return "text-success";
                }
            },
        );
    }

    /**
     * Get the radaccts for the customer.
     */
    public function radaccts()
    {
        return $this->hasMany(radacct::class, 'username', 'username');
    }

    /**
     * Get the Router for the customer.
     */
    public function router()
    {
        return $this->belongsTo(nas::class, 'router_id', 'id')->withDefault([
            'id' => 0,
            'nasname' => '0.0.0.0',
        ]);
    }

    /**
     * Customer Rate Limit.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function rateLimit(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['connection_type'] !== 'PPPoE') {
                    return $value;
                }

                if ($attributes['status'] !== 'fup') {
                    return $value;
                }

                $package = package::find($attributes['package_id']);

                $master_package = $package->master_package;

                if (fair_usage_policy::where('master_package_id', $master_package->id)->count()) {

                    $fair_usage_policy = fair_usage_policy::where('master_package_id', $master_package->id)->first();

                    return $fair_usage_policy->speed_limit;
                } else {

                    return 0;
                }
            },
        );
    }

    /**
     * Customer device Name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function remainingValidity(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['billing_type'] == 'Free') {
                    return 'Free';
                }

                switch ($attributes['connection_type']) {
                    case 'PPPoE':
                    case 'StaticIp':
                    case 'Other':
                        if ($attributes['billing_type'] == 'Monthly') {
                            $today = date(config('app.date_format'));
                            $exp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $attributes['package_expired_at'], getTimeZone($attributes['operator_id']), 'en')->format(config('app.date_format'));
                            if ($today == $exp) {
                                return getLangCode($attributes['operator_id']) == 'bn' ? 'আজ শেষ পেমেন্ট তারিখ' : 'Today is the last payment date';
                            }
                        }
                        break;

                    case 'Hotspot':
                        break;
                }

                $exp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $attributes['package_expired_at'], getTimeZone($attributes['operator_id']), 'en');
                $string = $exp->locale(getLangCode($attributes['operator_id']))->diffForHumans();
                if (Str::endsWith($string, 'আগে')) {
                    return $string . ' শেষ হয়েছে';
                }
                if (Str::endsWith($string, 'পরে')) {
                    return $string . ' শেষ হবে';
                }
                return $string;
            },
        );
    }

    /**
     * Customer Role Name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function role(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "customer",
        );
    }

    /**
     * Get the sms histories for the customer.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function smsHistories(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $where = [
                    ['operator_id', '=', $attributes['operator_id']],
                    ['customer_id', '=', $attributes['id']],
                ];
                return sms_history::where($where)->get();
            },
        );
    }

    /**
     * Get the user's status.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                switch ($attributes['connection_type']) {
                    case 'PPPoE':
                        if ($attributes['billing_type'] === 'Daily' && Carbon::createFromIsoFormat(config('app.expiry_time_format'), $attributes['package_expired_at'], getTimeZone($attributes['operator_id']), 'en')->lessThan(Carbon::now(getTimeZone($attributes['operator_id'])))) {
                            return 'suspended';
                        }
                        return $value;
                        break;

                    case 'Hotspot':
                        if (Carbon::createFromIsoFormat(config('app.expiry_time_format'), $attributes['package_expired_at'], getTimeZone($attributes['operator_id']), 'en')->lessThan(Carbon::now(getTimeZone($attributes['operator_id'])))) {
                            return 'suspended';
                        }
                        return $value;
                        break;

                    default:
                        return $value;
                        break;
                }
            },
        );
    }

    /**
     * Customer type.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function type(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {

                switch ($attributes['billing_type']) {
                    case 'Daily':
                        return match ($attributes['connection_type']) {
                            'PPPoE' => CustomerType::PPP_DAILY->name,
                            'Hotspot' => CustomerType::HOTPOST_DAILY->name,
                            'StaticIp' => CustomerType::STATIC_DAILY->name,
                            'Other' => CustomerType::OTHER_DAILY->name,
                        };
                        break;

                    case 'Monthly':
                        return match ($attributes['connection_type']) {
                            'PPPoE' => CustomerType::PPP_MONTHLY->name,
                            'Hotspot' => CustomerType::HOTPOST_MONTHLY->name,
                            'StaticIp' => CustomerType::STATIC_MONTHLY->name,
                            'Other' => CustomerType::OTHER_MONTHLY->name,
                        };
                        break;

                    case 'Free':
                        return match ($attributes['connection_type']) {
                            'PPPoE' => CustomerType::PPP_FREE->name,
                            'Hotspot' => CustomerType::HOTSPOT_FREE->name,
                            'StaticIp' => CustomerType::STATIC_FREE->name,
                            'Other' => CustomerType::OTHER_FREE->name,
                        };
                        break;
                }
            },
        );
    }

    /**
     * Customer Zone Name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function zone(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['zone_id'] < 1) {
                    return null;
                }
                $zone = CacheController::getZone($attributes['zone_id']);
                if ($zone) {
                    return $zone->name;
                } else {
                    return null;
                }
            },
        );
    }
}
