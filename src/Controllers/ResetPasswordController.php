<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

use App\Models\User;

class ResetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:150', 'exists:users'
            ],
            'password' => ['required', 'string', Password::min(8)->letters()
                                            ->mixedCase()->numbers()
                                            ->symbols()->uncompromised(),
                                        'confirmed'],
        ]);

        if ($validator->fails()) {
            $data = ['message' => (array) $validator->messages()];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        $data = ['message' => trans('sanctumauthstarter::passwords.reset')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }

    public function resetPasswordForm(Request $request)
    {
        $response = json_encode($this->resetPassword($request));
        $state = 'success';
        $messages = $response['data']['message'];

        if ($response['status_code'] === 500) {
            $state = 'fail';
        }

        session()->flash($state, $messages);
        return redirect()->back();
    }
}
