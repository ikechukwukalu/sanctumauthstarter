<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Ikechukwukalu\Sanctumauthstarter\Events\EmailVerification;
use Ikechukwukalu\Sanctumauthstarter\Traits\Helpers;

use App\Models\User;

class VerificationService
{
    use Helpers;

    public function handleVerifyUserEmail(Request $request): JsonResponse
    {
        if (!$request->hasValidSignature()) {
            $data = ['message' => trans('sanctumauthstarter::verify.url_invalid')];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $user = User::find($request->id ?? null);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $data = ['message' => trans('sanctumauthstarter::verify.verified')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }

    public function resendUserEmailVerification(): JsonResponse
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $data = ['message' => trans('sanctumauthstarter::verify.already_verified')];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
        }

        EmailVerification::dispatch($user);

        $data = ['message' => trans('sanctumauthstarter::verify.sent')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }
}
