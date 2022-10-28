<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Ikechukwukalu\Sanctumauthstarter\Rules\CurrentPassword;
use Ikechukwukalu\Sanctumauthstarter\Rules\DisallowOldPassword;

use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Ikechukwukalu\Sanctumauthstarter\Notifications\PasswordChange;

use App\Models\User;
use Ikechukwukalu\Sanctumauthstarter\Models\OldPassword;

class ChangePasswordController extends Controller
{
    public function __construct()
    {

    }

    /**
     * User change password.
     *
     * Within the config file, you are required to determine the number
     * of previously used passwords a User is not allowed to use anymore
     * by setting <b>password.check_all</b> to <b>TRUE/FALSE</b> or to an <b>int</b>
     * value and <b>password.number</b> to a corresponding <b>int</b>
     * value as well.
     *
     * You can choose to notify a User whenever a password is changed by setting
     * <b>password.notify.change</b> to <b>TRUE</b>
     *
     * @bodyParam current_password string required The user's password. Example: @wE3456qas@$
     * @bodyParam password string required The password for user authentication must contain uppercase, lowercase, symbols, numbers. Example: Ex@m122p$%l6E
     * @bodyParam password_confirmation string required Must match <small class="badge badge-blue">password</small> Field. Example: Ex@m122p$%l6E
     *
     * @response 200
     *
     * {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "message": string
     *  }
     * }
     *
     * @authenticated
     * @group Auth APIs
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', new CurrentPassword],
            'password' => ['required', 'string', 'max:16',
                            Password::min(8)
                                ->letters()->mixedCase()
                                ->numbers()->symbols()
                                ->uncompromised(),
                            'confirmed',
                            new DisallowOldPassword(
                                config('sanctumauthstarter.password.check_all', true),
                                config('sanctumauthstarter.password.number', 4)
                            )
                        ],
        ]);

        if ($validator->fails()) {
            $data = ['message' => (array) $validator->errors()->all()];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        if ($user->save()) {
            OldPassword::create([
                'user_id' => $user->id,
                'password' => Hash::make($request->password)
            ]);
        }

        if (config('sanctumauthstarter.password.notify.change', true)) {
            $user->notify(new PasswordChange());
        }

        $data = ['message' => trans('sanctumauthstarter::passwords.changed')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }
}
