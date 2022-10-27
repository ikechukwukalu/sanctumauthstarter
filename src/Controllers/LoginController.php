<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

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

    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * User form login.
     *
     * You can choose to notify a User whenever there has been a Login by setting
     * <b>password.notify.change</b> to <b>TRUE</b> within the config file.
     * Make sure to retrieve <small class="badge badge-blue">access_token</small> after login for User authentication
     *
     * @bodyParam email string required The email of the user. Example: johndoe@xyz.com
     * @bodyParam password string required The password for user authentication must contain uppercase, lowercase, symbols, numbers. Example: Ex@m122p$%l6E
     * @bodyParam remember_me int Could be set to 0 or 1. Example: 1
     *
     * @response 200 {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "message": string
     *      "access_token": string
     *  }
     * }
     *
     * @group No Auth APIs
     */
    public function login(Request $request): JsonResponse
    {
        if ($this->hasTooManyAttempts($request)) {
            $this->_fireLockoutEvent($request);

            $data = ["message" => trans('sanctumauthstarter::auth.throttle',
                        ['seconds' => $this->_limiter()
                            ->availableIn($this->_throttleKey($request))
                        ])
                    ];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $this->incrementAttempts($request);

        $credentials = Validator::make($request->all(), [
           'email' => ['required', 'email', 'max:200'],
           'password' => ['required', 'string']
        ]);

        if ($credentials->fails()) {

            $data = (array) $credentials->messages();
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $remember = isset($request->remember_me) ? true : false;

        if (!Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ], $remember))
        {

            $data = ['message' => trans('sanctumauthstarter::auth.failed')];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $this->clearAttempts($request);

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

        return $this->httpJsonResponse(
            trans('sanctumauthstarter::general.success'), 200, $data);
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
