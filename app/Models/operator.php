<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class operator extends Authenticatable implements
    MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The model type
     *
     * @var string|null (node|central)
     */
    protected $modelType = 'central';

    /**
     * Set connection for Central Model if (host_type === 'node')
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        if (config('local.host_type', 'central') === 'node') {
            if ($this->modelType === 'central') {
                $this->connection = config('database.central', 'mysql');
            }
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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'new_id' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['central_connection', 'node_connection', 'pgsql_connection', 'address', 'account_type_alias', 'account_balance', 'credit_balance', 'color', 'permissions', 'role_alias', 'readable_role'];

    /**
     * Get Account balance.
     * Will be used for Debit/Prepaid reseller's balance check
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function accountBalance(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['account_type'] !== 'debit') {
                    return 100;
                }

                $roles = ['operator', 'sub_operator'];

                if (!in_array($attributes['role'], $roles)) {
                    return 'N/A';
                }

                $where = [
                    ['account_owner', '=', $attributes['id']],
                    ['account_provider', '=', $attributes['gid']],
                ];

                $account = account::where($where)->firstOr(function () {
                    return account::make([
                        'account_provider' => 0,
                        'account_owner' => 0,
                        'balance' => 0,
                    ]);
                });

                return round($account->balance);
            }
        );
    }

    /**
     * Get the accounts that the operator owns
     */
    public function accountsOwns()
    {
        return $this->hasMany(account::class, 'account_owner', 'id');
    }

    /**
     * Get the accounts that the operator provides
     */
    public function accountsProvides()
    {
        return $this->hasMany(account::class, 'account_provider', 'id');
    }

    /**
     * Get credit balance
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function accountTypeAlias(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['account_type'] == 'debit') {
                    return 'Prepaid';
                } else {
                    return 'Postpaid';
                }
            },
        );
    }

    /**
     * Get address attribute
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function address(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $address = '';
                $address .=  $attributes['company'] . "<br>";
                if (strlen($attributes['house_no']) && strlen($attributes['road_no'])) {
                    $address .=  $attributes['house_no'] . "," . $attributes['road_no'] . "<br>";
                }
                if (strlen($attributes['district']) && strlen($attributes['postal_code'])) {
                    $address .=  $attributes['district'] . "," . $attributes['postal_code'] . "<br>";
                }
                $address .= "Helpline: " . $attributes['helpline'] . "<br>";
                return $address;
            },
        );
    }

    /**
     * Get the assigned packages for the operator.
     */
    public function allPackages()
    {
        if ($this->role == 'group_admin') {
            return $this->hasMany(package::class, 'mgid', 'id');
        }

        if ($this->role == 'manager') {
            return $this->hasMany(package::class, 'operator_id', 'gid');
        }

        return $this->hasMany(package::class, 'operator_id', 'id');
    }

    /**
     * Get the assigned master packages for the operator.
     */
    public function assigned_master_packages()
    {
        return $this->belongsToMany(master_package::class, 'packages', 'operator_id', 'mpid', 'id', 'id');
    }

    /**
     * Get the assigned packages for the operator.
     */
    public function assigned_packages()
    {
        if ($this->role == 'manager') {
            return $this->hasMany(package::class, 'operator_id', 'gid');
        }
        return $this->hasMany(package::class, 'operator_id', 'id');
    }

    /**
     * Get the Billing Profiles for the operator.
     */
    public function billing_profiles()
    {
        if ($this->role === 'group_admin') {
            return $this->hasMany(billing_profile::class, 'mgid', 'id');
        }

        if ($this->role === 'manager') {
            if ($this->mgid === $this->gid) {
                return $this->hasMany(billing_profile::class, 'mgid', 'mgid');
            } else {
                return $this->belongsToMany(billing_profile::class, 'billing_profile_operator', 'operator_id', 'billing_profile_id', 'gid', 'id');
            }
        }

        return $this->belongsToMany(billing_profile::class, 'billing_profile_operator', 'operator_id', 'billing_profile_id', 'id', 'id');
    }

    /**
     * Get the card_distributors for the operator.
     */
    public function card_distributors()
    {
        return $this->hasMany(card_distributor::class);
    }

    /**
     * Get central connection
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function centralConnection(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => config('database.central', 'mysql'),
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

                if ($attributes['provisioning_status'] == 0) {
                    $color = "text-danger";
                }

                if ($attributes['provisioning_status'] == 1) {
                    $color = "text-warning";
                }

                return $color;
            },
        );
    }

    /**
     * Get credit balance
     * Will be used for Credit/Postpaid reseller's Credit Limit check
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function creditBalance(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['account_type'] !== 'credit') {
                    return 100;
                }

                $roles = ['operator', 'sub_operator'];

                if (!in_array($attributes['role'], $roles)) {
                    return 'N/A';
                }

                $where = [
                    ['account_provider', '=', $attributes['id']],
                    ['account_owner', '=', $attributes['gid']],
                ];

                $account_payable = account::where($where)->firstOr(function () {
                    return account::make([
                        'account_provider' => 0,
                        'account_owner' => 0,
                        'balance' => 0,
                    ]);
                });

                return round($attributes['credit_limit'] - $account_payable->balance);
            },
        );
    }

    /**
     * Get the card_distributors for the operator.
     */
    public function complain_categories()
    {
        return $this->hasMany(complain_category::class);
    }

    /**
     * Get the country for the operator.
     */
    public function country()
    {
        return $this->belongsTo(country::class, 'country_id', 'id')->withDefault();
    }

    /**
     * Get the customers for the operator.
     */
    public function customers()
    {
        return $this->hasMany(all_customer::class);
    }

    /**
     * Get the customer zones for the operator.
     */
    public function customer_zones()
    {
        if ($this->role == 'manager') {
            return $this->hasMany(customer_zone::class, 'operator_id', 'gid');
        }
        return $this->hasMany(customer_zone::class);
    }

    /**
     * Get the customer_payments for the operator.
     */
    public function customer_payments()
    {
        return $this->hasMany(customer_payment::class);
    }

    /**
     * Get the custome fields for the operator.
     */
    public function custom_fields()
    {
        if ($this->role == 'manager') {
            return $this->hasMany(custom_field::class, 'operator_id', 'gid');
        }
        return $this->hasMany(custom_field::class);
    }

    /**
     * Get the devices for the operator.
     */
    public function departments()
    {
        if ($this->role == 'manager') {
            return $this->hasMany(department::class, 'operator_id', 'gid');
        }
        return $this->hasMany(department::class);
    }

    /**
     * Get the devices for the operator.
     */
    public function devices()
    {
        if ($this->role == 'manager') {
            return $this->hasMany(device::class, 'operator_id', 'gid');
        }
        return $this->hasMany(device::class);
    }

    /**
     * Get the devices for the operator.
     */
    public function due_date_reminders()
    {
        return $this->hasMany(due_date_reminder::class);
    }

    /**
     * Get the expense_categories for the operator.
     */
    public function expense_categories()
    {
        return $this->hasMany(expense_category::class);
    }

    /**
     * Get the expenses for the operator.
     */
    public function expenses()
    {
        return $this->hasMany(expense::class);
    }

    /**
     * Get the fair_usage_policies for the operator.
     */
    public function fair_usage_policies()
    {
        return $this->hasMany(fair_usage_policy::class, 'mgid', 'id');
    }

    /**
     * Get the group_admin associated with the operator.
     */
    public function group_admin()
    {
        return $this->hasOne(operator::class, 'id', 'gid');
    }

    /**
     * Get the group customers for the group admin.
     */
    public function group_customers()
    {
        return $this->hasMany(all_customer::class, 'mgid', 'id');
    }

    /**
     * Get the group operators for the operator.
     */
    public function group_operators()
    {
        return $this->hasMany(operator::class, 'gid', 'id');
    }

    /**
     * Get the ipv4pools for the operator.
     */
    public function ipv4pools()
    {
        return $this->hasMany(ipv4pool::class, 'mgid', 'id');
    }

    /**
     * Get the ipv6pools for the operator.
     */
    public function ipv6pools()
    {
        return $this->hasMany(ipv6pool::class, 'mgid', 'id');
    }

    /**
     * Get the pppoe_profiles for the operator.
     */
    public function pppoe_profiles()
    {
        return $this->hasMany(pppoe_profile::class, 'mgid', 'id');
    }

    /**
     * Get the group_admin associated with the operator.
     */
    public function master_admin()
    {
        return $this->hasOne(operator::class, 'id', 'mgid');
    }

    /**
     * Get the master_packages for the operator.
     */
    public function master_packages()
    {
        return $this->hasMany(master_package::class, 'mgid', 'id');
    }

    /**
     * Get node connection
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function nodeConnection(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $attributes['radius_db_connection'];
            },
        );
    }

    /**
     * Get the operators for the group_admin.
     */
    public function operators()
    {
        if ($this->role == 'group_admin') {
            return $this->hasMany(operator::class, 'mgid', 'id');
        }

        return $this->hasMany(operator::class, 'gid', 'id');
    }

    /**
     * Get the assigned packages for the operator.
     */
    public function packages()
    {
        if ($this->role == 'manager') {
            return $this->hasMany(package::class, 'operator_id', 'gid');
        }
        return $this->hasMany(package::class, 'operator_id', 'id');
    }

    /**
     * Get the payment_gateways for the operator.
     */
    public function payment_gateways()
    {
        return $this->hasMany(payment_gateway::class);
    }

    /**
     * Get the customer_payments for the operator.
     */
    public function payment_sends()
    {
        return $this->hasMany(pending_transaction::class, 'account_provider', 'id');
    }

    /**
     * Get the customer_payments for the operator.
     */
    public function payment_receives()
    {
        return $this->hasMany(pending_transaction::class, 'account_owner', 'id');
    }

    /**
     * Get credit balance
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function pgsqlConnection(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $attributes['radius_db_connection'] . 'pgsql';
            },
        );
    }

    /**
     * Get manager's Permissions
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function permissions(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                $collection = [];

                if ($attributes['role'] === 'manager' || $attributes['role'] === 'operator' || $attributes['role'] === 'sub_operator') {

                    $permissions = operator_permission::where('operator_id', $attributes['id'])->get();

                    foreach ($permissions as $permission) {

                        $collection[] = $permission->permission;
                    }
                }

                return collect($collection);
            },
        );
    }

    /**
     * Get role alias
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function roleAlias(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['role'] === 'operator') {

                    if ($attributes['gid'] === $attributes['mgid']) {
                        return $attributes['role'];
                    } else {
                        return 'sub_operator';
                    }
                }

                return $attributes['role'];
            },
        );
    }

    /**
     * Get readable role
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function readableRole(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                switch ($attributes['role']) {

                    case 'super_admin':
                        return 'Super Admin';
                        break;

                    case 'group_admin':
                        return 'Group Admin';
                        break;

                    case 'operator':
                        return 'Reseller';
                        break;

                    case 'sub_operator':
                        return 'Sub-Reseller';
                        break;

                    default:
                        return $attributes['role'];
                        break;
                }
            },
        );
    }

    /**
     * Get the recharge_cards record associated with the operator.
     */
    public function recharge_cards()
    {
        return $this->hasMany(recharge_card::class);
    }

    /**
     * Get the sms_gateway record associated with the operator.
     */
    public function sms_gateway()
    {
        return $this->hasOne(sms_gateway::class);
    }

    /**
     * Get the sms_histories for the operator.
     */

    public function sms_histories()
    {
        return $this->hasMany(sms_history::class);
    }

    /**
     * Get the sms_bills for the operator.
     */

    public function sms_bills()
    {
        return $this->hasMany(sms_bill::class);
    }

    /**
     * Get the sms_payments for the operator.
     */

    public function sms_payments()
    {
        return $this->hasMany(sms_payment::class);
    }

    /**
     * Get the subscription_bills for the operator.
     */

    public function subscription_bills()
    {
        return $this->hasMany(subscription_bill::class, 'mgid', 'id');
    }

    /**
     * Get the subscription_payments for the operator.
     */

    public function subscription_payments()
    {
        return $this->hasMany(subscription_payment::class, 'mgid', 'id');
    }
}
