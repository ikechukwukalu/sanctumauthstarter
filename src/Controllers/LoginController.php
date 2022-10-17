<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Ikechukwukalu\Sanctumauthstarter\Notifications\UserLogin;
use Carbon\Carbon;
use hisorange\BrowserDetect\Parser as Browser;

use App\Models\User;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected $maxAttempts = 5; // change to the max attempt you want.
    protected $delayMinutes = 1;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function login(Request $request): JsonResponse
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $data = ["message" => trans('sanctumauthstarter::auth.throttle',
                        ['seconds' => $this->limiter()->availableIn(
                            $this->throttleKey($request))
                        ])
                    ];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $credentials = Validator::make($request->all(), [
           'email' => ['required', 'email', 'max:200'],
           'password' => ['required', 'string']
        ]);

        if ($credentials->fails()) {
            $this->incrementLoginAttempts($request);

            $data = (array) $credentials->messages();
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $remember = isset($request->remember_me) ? true : false;

        if (Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ], $remember))
        {
            $this->clearLoginAttempts($request);

            $user = Auth::user();
            $token = $user->createToken($request->email);

            if (config('sanctumauthstarter.login.notify.user', true)) {
                $now = Carbon::now();
                $time = $now->isoFormat('MMMM Do YYYY, h:mm:ss a');
                $device = $this->getLoginUserInformation();

                $user->notify(new UserLogin($time, $device));
            }

            $data = [
                'access_token' => $token->plainTextToken,
                'message' => trans('sanctumauthstarter::auth.success')
            ];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
        }

        $this->incrementLoginAttempts($request);

        $data = ['message' => trans('sanctumauthstarter::auth.failed')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
    }

    private function getLoginUserInformation(): array
    {
        $info = [];

        if (Browser::deviceType() === 'Mobile' ||
            Browser::deviceType() === 'Tablet') {
            $info = [
                Browser::deviceFamily(),
                Browser::deviceModel()
            ];
        }

        if (Browser::deviceType() === 'Desktop') {
            $info = [
                Browser::browserName(),
                Browser::platformName()
            ];
        }

        if (Browser::deviceType() === 'Bot') {
            $info = [
                Browser::userAgent()
            ];
        }

        return $info;
    }
}
