<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatPrivateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $userSend;
    public $userReciever;
    public $message;
    public function __construct(User $userSend,User $userReciever,$message)
    {
        $this -> userSend = $userSend;
        $this -> userReciever = $userReciever;
        $this -> message = $message;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn():array
    {
        return [
            new PrivateChannel('chat.private.'.$this->userSend->id .".".$this->userReciever->id)
        ];
            
        
    }
 
}
