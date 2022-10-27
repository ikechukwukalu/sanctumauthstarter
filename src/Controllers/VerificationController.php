<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class VerificationController extends Controller
{

    /**
     * User email verification.
     *
     * This endpoint must have a valid laravel generated URL signature to work.
     * It is automatically sent after a successful registration and
     * <b>registration.notify.verify</b> is set to <b>TRUE</b> within the config file.
     *
     * @queryParam id string required <small class="badge badge-blue">id</small> Field must belong to a registered User. Example: 1
     *
     * @response 200 {
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
    public function verifyUserEmail(Request $request): JsonResponse
    {
        if (!$request->hasValidSignature()) {
            $data = ['message' => trans('sanctumauthstarter::verify.url_invalid')];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $user = User::find($request->id);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $data = ['message' => trans('sanctumauthstarter::verify.verified')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }

    /**
     * User resend email verification.
     *
     * This endpoint is used to generate and send via email a URL for User email verification to a registered User.
     * It is automatically sent after a successful registration and
     * <b>registration.notify.verify</b> is set to <b>TRUE</b> within the config file.
     *
     * @queryParam id string required <small class="badge badge-blue">id</small> Field must belong to a registered User. Example: 1
     *
     * @response 200 {
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
    public function resendUserEmailVerification(): JsonResponse
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $data = ['message' => trans('sanctumauthstarter::verify.already_verified')];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $user->sendEmailVerificationNotification();

        $data = ['message' => trans('sanctumauthstarter::verify.sent')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }
}
