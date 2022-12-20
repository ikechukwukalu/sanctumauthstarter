<?php

use Illuminate\Support\Facades\Broadcast;
use Ikechukwukalu\Sanctumauthstarter\Models\SocialiteUserDeviceLogin;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('access.token.{userUUID}', function ($user, $userUUID) {
    $id = SocialiteUserDeviceLogin::where('user_uuid', $userUUID)
            ->first()->user_id;

    return $user->id === $id;
});
