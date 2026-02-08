<?php

namespace App\Policies;

use App\Http\Controllers\CacheController;
use App\Models\card_distributor;
use App\Models\Freeradius\customer;
use App\Models\recharge_card;
use Illuminate\Auth\Access\HandlesAuthorization;

class CanTheCardBeUsedPolicy
{
    use HandlesAuthorization;

    /**
     * The customer instance.
     *
     * @var \App\Models\Freeradius\customer $customer
     */
    protected $customer;

    /**
     * The recharge_card instance.
     *
     * @var \App\Models\recharge_card $recharge_card
     */
    protected $recharge_card;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct(customer $customer, recharge_card $recharge_card)
    {
        $this->customer = $customer;
        $this->recharge_card = $recharge_card;
    }

    public function canUseRechargeCard()
    {
        $customer = $this->customer;
        $recharge_card = $this->recharge_card;

        // not used
        if ($recharge_card->status == 'used') {
            return $this->deny('The recharge card has already been used.');
        }

        // not locked
        if ($recharge_card->locked == 1) {
            return $this->deny('The recharge card is locked.');
        }

        // operator_id
        if ($customer->operator_id !== $recharge_card->operator_id) {
            return $this->deny('The customer operator and the card operator should be the same.');
        }

        // connection_type
        $package = CacheController::getPackage($recharge_card->package_id);
        if (!$package) {
            return $this->deny('Package Not Found.');
        }
        if ($customer->connection_type != $package->master_package->connection_type) {
            return $this->deny('The connection type must be same for the customer and the package. We found the customer connection type: ' . $customer->connection_type . ' but the package connection type: ' . $package->master_package->connection_type);
        }

        // card distributor has balance
        $card_distributor = card_distributor::find($recharge_card->card_distributor_id);
        if (!$card_distributor) {
            return $this->deny('Card Distributor Not Found.');
        }

        if ($card_distributor->account_type == 'prepaid') {
            if ($card_distributor->account_balance < $package->price) {
                return $this->deny('The balance of the card distributor is low.');
            }
        }

        // operator has balance
        $operator = CacheController::getOperator($recharge_card->operator_id);
        if (CustomerPolicy::hasBalance($operator) == false) {
            return $this->deny('The balance of the card operator is low.');
        }

        return true;
    }
}
