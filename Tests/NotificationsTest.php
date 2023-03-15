<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Ikechukwukalu\Sanctumauthstarter\Models\TestUser;
use Ikechukwukalu\Sanctumauthstarter\Notifications\PasswordChange;
use Ikechukwukalu\Sanctumauthstarter\Notifications\UserLogin;
use Ikechukwukalu\Sanctumauthstarter\Notifications\WelcomeUser;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_fires_user_notifications(): void
    {
        Notification::fake();

        Notification::assertNothingSent();

        $user = TestUser::create([
            'name' => str::random(),
            'email' => Str::random(40) . '@example.com',
            'password' => Hash::make('password')
        ]);

        $this->actingAs($user);
        $user->notify(new PasswordChange());
        $user->notify(new WelcomeUser($user));

        $time = Carbon::now()->isoFormat('Do of MMMM YYYY, h:mm:ssa');
        $user->notify(new UserLogin($time, []));

        Notification::assertSentTo(
            [$user], PasswordChange::class
        );

        Notification::assertSentTo(
            [$user], WelcomeUser::class
        );

        Notification::assertSentTo(
            [$user], UserLogin::class
        );

        Notification::assertCount(3);
    }
}
