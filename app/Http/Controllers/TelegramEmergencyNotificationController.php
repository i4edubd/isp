<?php

namespace App\Http\Controllers;

use App\Http\Controllers\enum\TelegramChatsType;
use App\Models\telegraph_chat;
use Illuminate\Support\Facades\Log;

class TelegramEmergencyNotificationController extends Controller
{
    /**
     * Send Emergency Notification
     *
     * @param  string  $message
     * @return void
     */
    public static function send(string $message)
    {
        $chats = telegraph_chat::where('name', TelegramChatsType::EmergencyNotification->name)->get();

        foreach ($chats as $chat) {
            $response = $chat->html($message)->send();
            if ($response->telegraphError()) {
                Log::channel('telegram_bot')->debug($response);
            }
        }
    }
}
