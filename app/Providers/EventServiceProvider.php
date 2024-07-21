<?php

namespace App\Providers;

use App\Events\ForgotPasswordEvent;
use App\Events\UserLogginEvent;
use App\Events\UserRegisterSuccessEvent;
use App\Listeners\SendMailForgotPasswordListener;
use App\Listeners\UserLoginListener;
use App\Listeners\UserRegisterSuccessListener;
use App\Listeners\VerifyEmailAfterUserRegisterSuccess;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserRegisterSuccessEvent::class=>[
            VerifyEmailAfterUserRegisterSuccess::class,
        ],
        ForgotPasswordEvent::class=>[
            SendMailForgotPasswordListener::class,
        ],
        UserLogginEvent::class=>[
            UserLoginListener::class
        ]
        
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
