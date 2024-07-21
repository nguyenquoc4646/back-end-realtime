<?php

namespace App\Listeners;

use App\Events\UserLogginEvent;
use App\Models\UserLoginModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UserLoginListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLogginEvent $event): void
    {
        $UserLogin = new UserLoginModel;
        $UserLogin->user_id = $event->user->id;
        $UserLogin->login_at = now();
        $UserLogin->save();
    
    }
}
