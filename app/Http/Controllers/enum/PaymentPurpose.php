<?php

namespace App\Http\Controllers\enum;

enum PaymentPurpose: string
{
    case PACKAGE_PURCHASE = 'Package Purchase/Recharge';
    case PACKAGE_CHANGE = 'Package Change';
    case PACKAGE_DOWNGRADE = 'Package Downgrade';
    case PACKAGE_UPGRADE = 'Package Upgrade';

    case NEW_CUSTOMER = 'New customer';

    case MONTHLY_BILL = 'Monthly Bill';

    case OTHER_PAYMENT = 'Other Payment';

    case BILLING_PROFILE_CHANGE = 'Payment Date Change';

    case PAYMENT_FROM_ADVANCE = 'Payment From Advance';
    case PAYMENT_AFTER_ADVANCE = 'Payment after deducting advance';
}
