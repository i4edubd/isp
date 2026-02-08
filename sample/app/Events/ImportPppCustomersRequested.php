<?php

namespace App\Events;

use App\Models\customer_import_request;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportPppCustomersRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * The customer_import_request instance.
     *
     * @var \App\Models\customer_import_request
     */
    public $customer_import_request;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(customer_import_request $customer_import_request)
    {
        $this->customer_import_request = $customer_import_request;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
         return new PrivateChannel('channel-name');
    }
}
