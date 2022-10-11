<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class VerificationController extends Controller
{
    public function verifyUserEmail(Request $request): JsonResponse
    {
        if (!$request->hasValidSignature()) {
            $data = ['message' => trans('sanctumauthstarter::verify.url_invalid')];
            return $this->httpJsonResponse('fail', 500, $data);
        }

        $user = User::find($request->id);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $data = ['message' => trans('sanctumauthstarter::verify.verified')];
        return $this->httpJsonResponse('success', 200, $data);
    }

    public function resendUserEmailVerification(): JsonResponse
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $data = ['message' => trans('sanctumauthstarter::verify.already_verified')];
            return $this->httpJsonResponse('fail', 500, $data);
        }

        $user->sendEmailVerificationNotification();

        $data = ['message' => trans('sanctumauthstarter::verify.sent')];
        return $this->httpJsonResponse('success', 200, $data);
    }
}
