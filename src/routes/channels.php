<?php

use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel(
    config('sanctumauthstarter.channels.access_token',
        'access.token.{userUUID}'),
function ($user, $userUUID) {
    $id = \Ikechukwukalu\Sanctumauthstarter\Models\SocialiteUserDeviceLogin::where('user_uuid', $userUUID)
            ->first()->user_id;

    return $user->id === $id;
});
