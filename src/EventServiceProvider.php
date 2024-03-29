<?php

namespace Ikechukwukalu\Sanctumauthstarter;

use Ikechukwukalu\Sanctumauthstarter\Events\ForgotPassword;
use Ikechukwukalu\Sanctumauthstarter\Events\EmailVerification;
use Ikechukwukalu\Sanctumauthstarter\Events\SocialiteLogin;
use Ikechukwukalu\Sanctumauthstarter\Events\TwoFactorLogin;
use Ikechukwukalu\Sanctumauthstarter\Listeners\SendResetLink;
use Ikechukwukalu\Sanctumauthstarter\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        ForgotPassword::class => [
            SendResetLink::class
        ],
        EmailVerification::class => [
            SendEmailVerificationNotification::class
        ],
        TwoFactorLogin::class => [
        ],
        SocialiteLogin::class => [
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
