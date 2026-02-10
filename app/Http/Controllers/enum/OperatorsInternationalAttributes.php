<?php

namespace App\Http\Controllers\enum;

enum OperatorsInternationalAttributes: string
{
    case country_id = 'ISO 2 Country Code, Currency code and Phone Code';
    case timezone = 'Time Zone';
    case lang_code = 'Language Codes';
}
