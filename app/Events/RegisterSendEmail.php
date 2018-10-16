<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/5/7
 * Time: 9:41
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RegisterSendEmail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $email;

    /**
     * Create a new event instance.
     * @param string $email
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
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