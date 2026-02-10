<?php

namespace App\Http\Controllers\enum;

enum BillingTerms: string
{
    case INTERVAL_UNIT_MINUTE = 'Minute';
    case INTERVAL_UNIT_DAY = 'Day';

    case INTERVAL_COUNT = "0";

    case BILL_PERIOD_SHORT = 'date("F-Y")';
    case BILL_PERIOD_LONG = 'From date("Y-m-d") To date("Y-m-d")';
}
