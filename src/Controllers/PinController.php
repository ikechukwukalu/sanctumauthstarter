<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Ikechukwukalu\Sanctumauthstarter\Rules\CurrentPin;
use Ikechukwukalu\Sanctumauthstarter\Rules\DisallowOldPin;

use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Ikechukwukalu\Sanctumauthstarter\Notifications\PinChange;

use App\Models\User;
use Ikechukwukalu\Sanctumauthstarter\Models\RequirePin;
use Ikechukwukalu\Sanctumauthstarter\Models\OldPin;

class PinController extends Controller
{
    public function __construct()
    {

    }

    public function changePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_pin' => ['required', 'string', new CurrentPin(true)],
            'pin' => [
                        'required', 'string',
                        'max:' . config('sanctumauthstarter.pin.max', 4),
                        Password::min(config('sanctumauthstarter.pin.min', 4))->numbers(),
                        'confirmed',
                        new DisallowOldPin(
                            config('sanctumauthstarter.pin.check_all', true),
                            config('sanctumauthstarter.pin.number', 4)
                        )
                    ],
        ]);

        if ($validator->fails()) {
            $data = ['message' => (array) $validator->messages()];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $user = Auth::user();
        $user->pin = Hash::make($request->pin);
        $user->default_pin = $request->current_pin !== config('sanctumauthstarter.pin.default', '0000');

        if ($user->save()) {
            OldPin::create([
                'user_id' => $user->id,
                'pin' => Hash::make($request->pin)
            ]);
        }

        if (config('sanctumauthstarter.pin.notify.change', true)) {
            $user->notify(new PinChange());
        }

        $data = ['message' => trans('sanctumauthstarter::pin.changed')];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }

    public function pinRequired(Request $request, $uuid)
    {
        if (!$request->hasValidSignature()) {
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'),
                    401, ['message' => trans('sanctumauthstarter::pin.expired_url')]);
        }

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            config('sanctumauthstarter.pin.input', '_pin') => ['required', 'string', new CurrentPin],
        ]);

        if ($validator->fails()) {
            $data = ['message' => (array) $validator->messages()];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $requirePin = RequirePin::whereBelongsTo($user)
                        ->where('uuid', $uuid)
                        ->whereNull('approved_at')
                        ->whereNull('cancelled_at')
                        ->first();

        if (!isset($requirePin->id)) {
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'),
                    401, ['message' => trans('sanctumauthstarter::pin.invalid_url')]);
        }

        if (config('sanctumauthstarter.pin.verify_sender', true)) {
            if (
                $requirePin->ip !== $this->getClientIp($request) ||
                $requirePin->device !== $request->userAgent()
            ) {
                return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'),
                        406, ['message' => trans('sanctumauthstarter::pin.unverified_sender')]);
            }
        }

        $this->updateRequest($request, unserialize($requirePin->payload));

        $request = Request::create($requirePin->route_arrested, $requirePin->method, ['_uuid' => $uuid]);
        $response = Route::dispatch($request);

        return $response;
    }

    public function pinValidationURL(string $url): JsonResponse
    {
        return $this->httpJsonResponse(
            trans('sanctumauthstarter::general.success'), 200,
            [
                'message' => trans('sanctumauthstarter::pin.require_pin'),
                'url' => $url
            ]
        );
    }

    private function updateRequest(Request $request, array $payload): void
    {
        $request->merge([
            'expires' => null,
            'signature' => null,
            config('sanctumauthstarter.pin.input', '_pin') => null
        ]);

        foreach($payload as $key => $item) {
            $request->merge([$key => $payload[$key]]);
        }
    }
}
