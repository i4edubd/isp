<?php

namespace App\Http\Controllers\enum;


enum CustomerOverallStatus
{
    case PAID_ACTIVE;
    case PAID_SUSPENDED;
    case PAID_DISABLED;

    case BILLED_ACTIVE;
    case BILLED_SUSPENDED;
    case BILLED_DISABLED;
}
