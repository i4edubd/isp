<?php

namespace App\Http\Controllers\enum;

enum TelegramChatsType
{
    case EmergencyNotification;
    case SoftwareSupport;
    case CustomerSupport;
}
