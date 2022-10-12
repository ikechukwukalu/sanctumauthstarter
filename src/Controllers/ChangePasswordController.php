<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Ikechukwukalu\Sanctumauthstarter\Rules\CurrentPassword;
use Ikechukwukalu\Sanctumauthstarter\Rules\DisallowOldPassword;
use Ikechukwukalu\Sanctumauthstarter\Models\OldPassword;

use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

use App\Models\User;

class ChangePasswordController extends Controller
{
    public function __construct()
    {

    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', new CurrentPassword],
            'password' => ['required', 'string', 'max:16', Password::min(8)
                                            ->letters()->mixedCase()
                                            ->numbers()->symbols()
                                            ->uncompromised(),
                                        'confirmed', new DisallowOldPassword],
        ]);

        if ($validator->fails()) {
            $data = ['message' => (array) $validator->messages()];
            return $this->httpJsonResponse('fail', 500, $data);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        if ($user->save()) {
            OldPassword::create([
                'user_id' => $user->id,
                'password' => Hash::make($request->password)
            ]);
        }

        $data = ['message' => trans('sanctumauthstarter::passwords.changed')];
        return $this->httpJsonResponse('success', 200, $data);
    }
}
