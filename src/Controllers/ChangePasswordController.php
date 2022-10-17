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
            $data = ['message' => (array) $validator->messages()];
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

        if (config('sanctumauthstarter.registration.notify.pin', true)) {
            $user->notify(new PasswordChange());
        }

        $data = ['message' => trans('sanctumauthstarter::passwords.changed')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }
}
