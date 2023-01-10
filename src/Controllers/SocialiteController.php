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
     * This API accepts a <b>UUID</b> value as a url param and stores it as <b>user_uuid</b>
     * and as a unique value together with the following params: <b>ip_address</b>, <b>user_agent</b>
     * into the database
     *
     * It sets the <b>UUID</b> as a cookie and returns a view.
     *
     * The page returned redirects to the Oauth URL - <small class="badge badge-blue">/auth/redirect</small>
     *  using JavaScript.
     *
     * @header Accept text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*\/*;q=0.8,application/signed-exchange;v=b3;q=0.9
     * @header Content-Type text/html; charset=UTF-8
     *
     * @urlParam uuid string required Example: 149c283b-2e63-440b-bd62-d411674881ee
     *
     * @response 200 return view('sanctumauthstarter::cookie.setcookie')
     *
     *
     *
     * @group Web URLs
     */
    public function setCookie(Request $request) {
        $socialiteUser = SocialiteUserDeviceLogin::where("user_uuid", $request->uuid)
                            ->first();

        if (isset($socialiteUser->user_uuid))
        {
            abort(440, trans('sanctumauthstarter::cookie.error_440'));
        }

        SocialiteUserDeviceLogin::create([
                "user_uuid" => $request->uuid,
                "ip_address" => $this->getClientIp($request),
                "user_agent" => $request->userAgent(),
            ]
        );

        return response(view('sanctumauthstarter::cookie.setcookie'))->cookie(config('sanctumauthstarter.cookie.name', 'user_uuid'), $request->uuid, config('sanctumauthstarter.cookie.minutes', 5));
    }

    /**
     * User Oauth authentication redirect.
     *
     * @header Accept text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*\/*;q=0.8,application/signed-exchange;v=b3;q=0.9
     * @header Content-Type text/html; charset=UTF-8
     *
     * @response 302 redirects to <small class="badge badge-blue">/auth/callback</small>
     *
     * @group Web URLs
     */
    public function authRedirect(Request $request) {
        return Socialite::driver('google')->redirect();
    }

    /**
     * User Oauth authentication callback.
     *
     * Retrieves Oauth authenticated user details, registers and logins the
     * user.
     *
     * Retrieves <b>UUID</b> from cookie and updates the user details in the database.
     *
     * Creates user <b>access_token</b> and broadcasts the following payload <b>user_id</b>, <b>access_token</b> to the
     * unique public channel created with the unique <b>UUID</b>
     *
     * @header Accept text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*\/*;q=0.8,application/signed-exchange;v=b3;q=0.9
     * @header Content-Type text/html; charset=UTF-8
     *
     * @response 200
     *
     * {
     * "access_token": "1|mtnWTrh2Am6PWJ991wYB0rewVtROKxkuSiWEY836",
     * "user_id": 1,
     * }
     *
     * @group Web URLs
     */
    public function authCallback(Request $request) {
        $userUUID = $request->cookie(config('sanctumauthstarter.cookie.name', 'user_uuid'));
        if (!$userUUID) {
            abort(440, trans('sanctumauthstarter::cookie.error_440'));
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
            $time = $now->isoFormat('Do of MMMM YYYY, h:mm:ssa');
            $deviceAndLocation = $this->getLoginUserInformation();

            $user->notify(new UserLogin($time, $deviceAndLocation));
        }

        SocialiteUserDeviceLogin::where('user_uuid', $userUUID)->update([
            "user_id" => $user->id,
            "user_email" => $user->email
        ]);

        SocialiteLogin::dispatch($user, $token, $userUUID);

        return view('sanctumauthstarter::socialite.callback', ['user' => $user]);
    }
}
