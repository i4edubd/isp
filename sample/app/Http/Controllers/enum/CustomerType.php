<?php

namespace App\Http\Controllers\enum;

enum CustomerType
{
    case PPP_FREE;
    case PPP_DAILY;
    case PPP_MONTHLY;

    case HOTSPOT_FREE;
    case HOTPOST_DAILY;
    case HOTPOST_MONTHLY;

    case STATIC_FREE;
    case STATIC_DAILY;
    case STATIC_MONTHLY;

    case OTHER_FREE;
    case OTHER_DAILY;
    case OTHER_MONTHLY;

    public function isAllowed(): bool
    {
        return match ($this) {
            CustomerType::PPP_FREE => true,
            CustomerType::PPP_DAILY => true,
            CustomerType::PPP_MONTHLY => true,

            CustomerType::HOTSPOT_FREE => true,
            CustomerType::HOTPOST_DAILY => true,
            CustomerType::HOTPOST_MONTHLY => false,

            CustomerType::STATIC_FREE => true,
            CustomerType::STATIC_DAILY => false,
            CustomerType::STATIC_MONTHLY => true,

            CustomerType::OTHER_FREE => true,
            CustomerType::OTHER_DAILY => false,
            CustomerType::OTHER_MONTHLY => true,

            default => false,
        };
    }

    public function description(): string
    {
        return match ($this) {
            CustomerType::PPP_FREE => 'No monthly bill, no automatic suspension, users of group administrators only.',
            CustomerType::PPP_DAILY => 'No monthly bill, Pay as you go.',
            CustomerType::PPP_MONTHLY => 'Pay monthly anytime during the month.',

            CustomerType::HOTSPOT_FREE => 'No monthly bill, no automatic suspension, users of group administrators only.',
            CustomerType::HOTPOST_DAILY => 'Buy package, No monthly bill.',
            CustomerType::HOTPOST_MONTHLY => 'Not allowed.',

            CustomerType::STATIC_FREE => 'No monthly bill, no automatic suspension, users of group administrators only.',
            CustomerType::STATIC_DAILY => 'Not allowed.',
            CustomerType::STATIC_MONTHLY => 'Pay monthly anytime during the month, users of group administrators only.',

            CustomerType::OTHER_FREE => 'No monthly bill, no automatic suspension, users of group administrators only.',
            CustomerType::OTHER_DAILY => 'Not allowed.',
            CustomerType::OTHER_MONTHLY => 'Pay monthly anytime during the month',

            default => 'Not allowed.',
        };
    }
}
