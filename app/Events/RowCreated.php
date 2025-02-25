<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RowCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $row;

    public function __construct($row)
    {
        $this->row = $row;
    }

    public function broadcastOn()
    {
        return new Channel('rows');
    }

    public function broadcastAs()
    {
        return 'row.created';
    }
}