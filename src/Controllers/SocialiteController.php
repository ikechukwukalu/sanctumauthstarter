<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Ikechukwukalu\Sanctumauthstarter\Notifications\UserLogin;
use Carbon\Carbon;
use hisorange\BrowserDetect\Parser as Browser;
use Ikechukwukalu\Sanctumauthstarter\Events\SocialiteLogin;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Cookie;

use App\Models\User;
use Ikechukwukalu\Sanctumauthstarter\Models\SocialiteUserDeviceLogin;

class SocialiteController extends Controller
{


    /**
     * Set user uuid cookie.
     *
     * This API accepts a uuid value as a url param
     * sets it as aa cookie and saves it into the database
     * and redirects to the Oauth URL
     *
     * @urlParam uuid string required Example: 149c283b-2e63-440b-bd62-d411674881ee
     *
     * @response 302 redirects to /auth/redirect
     *
     *
     *
     * @group Web APIs
     */
    public function setCookie(Request $request) {
        SocialiteUserDeviceLogin::firstOrCreate(
            ["user_uuid" => $request->uuid],
            [
                "user_uuid" => $request->uuid,
                "ip_address" => $this->getClientIp($request),
                "user_agent" => $request->userAgent(),
            ]
        );

        return response(view('sanctumauthstarter::cookie.setcookie'))->cookie('user_uuid', $request->uuid, 30);
    }

    /**
     * User Oauth authentication redirect.
     *
     * URL for Oauth authentication
     *
     * @response 302 redirects to /auth/callback
     *
     * @group Web APIs
     */
    public function authRedirect(Request $request) {
        return Socialite::driver('google')->redirect();
    }

    /**
     * User Oauth authentication callback.
     *
     * URL for Oauth user details retrieval
     *
     * @response 200 via websocket
     *
     * {
     * "access_token": "1|mtnWTrh2Am6PWJ991wYB0rewVtROKxkuSiWEY836",
     * "user_id": 1,
     * }
     *
     * @group Web APIs
     */
    public function authCallback(Request $request) {
        $userUUID = $request->cookie('user_uuid');
        if (!$userUUID) {
            abort(440, 'Wrong user credential!');
        }

        $google = Socialite::driver('google')->user();

        $user = User::updateOrCreate([
            'email' => $google->email,
        ], [
            'name' => $google->name,
            'email' => $google->email,
            'socialite_signup' => true,
            'form_signup' => false
        ]);

        Auth::login($user);
        $token = $user->createToken($user->email);

        if (config('sanctumauthstarter.login.notify.user', true)) {
            $now = Carbon::now();
            $time = $now->isoFormat('MMMM Do YYYY, h:mm:ss a');
            $device = $this->getLoginUserInformation();

            $user->notify(new UserLogin($time, $device));
        }

        SocialiteUserDeviceLogin::where('user_uuid', $userUUID)->update([
            "user_id" => $user->id,
            "user_email" => $user->email
        ]);

        SocialiteLogin::dispatch($user, $token, $userUUID);
    }
}
