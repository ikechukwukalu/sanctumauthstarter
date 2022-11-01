<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Ikechukwukalu\Sanctumauthstarter\Events\ForgotPassword;

use App\Models\User;

class ForgotPasswordController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * User forgot password.
     *
     * The user must enter a registered email.
     *
     * @bodyParam email string required The email of the user. Example: johndoe@xyz.com
     *
     * @response 200 {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "message": string
     *  }
     * }
     *
     * @group No Auth APIs
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $credentials = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:150', 'exists:users'
            ]
        ]);

        if ($credentials->fails()) {
            $data = ["message" => (array) $credentials->messages()];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $user = User::where("email", $request->email)->first();
        if (isset($user->email)) {
            ForgotPassword::dispatch($user);
        }

        $data = ['message' => trans('sanctumauthstarter::passwords.sent')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }
}
