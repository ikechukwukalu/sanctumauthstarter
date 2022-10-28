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
use Illuminate\Support\Facades\Crypt;
use Ikechukwukalu\Sanctumauthstarter\Notifications\PinChange;

use App\Models\User;
use Ikechukwukalu\Sanctumauthstarter\Models\RequirePin;
use Ikechukwukalu\Sanctumauthstarter\Models\OldPin;

class PinController extends Controller
{
    protected $maxAttempts = 3;
    protected $delayMinutes = 1;

    public function __construct()
    {
        $this->maxAttempts = config('sanctumauthstarter.pin.maxAttempts', 3);
        $this->delayMinutes = config('sanctumauthstarter.pin.delayMinutes', 1);
    }

    /**
     * User change pin.
     *
     * Within the config file, you are required to determine the number
     * of previously used pins a User is not allowed to use anymore
     * by setting <b>pin.check_all</b> to <b>TRUE/FALSE</b> or to an <b>int</b>
     * value and <b>pin.number</b> to a corresponding <b>int</b>
     * value as well.
     *
     * You can choose to notify a User whenever a pin is changed by setting
     * <b>pin.notify.change</b> to <b>TRUE</b>
     *
     * @bodyParam current_pin string required The user's pin. Example: @wE3456qas@$
     * @bodyParam pin string required The pin for user authentication must contain only numbers. Example: Ex@m122p$%l6E
     * @bodyParam pin_confirmation string required Must match <small class="badge badge-blue">pin</small> Field. Example: Ex@m122p$%l6E
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
            $data = ['message' => (array) $validator->errors()->all()];
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

    /**
     * User pin authentication.
     *
     * @bodyParam _pin string required The user's pin must contain only numbers. Example: 0000
     * @urlParam uuid string required Example: eab8cce0-bb22-4c53-8924-b885ebb67f5a
     *
     * @authenticated
     * @group Auth APIs
     * @subgroup Require Pin APIs
     * @subgroupDescription <b>require.pin</b> middleware can
     * be added to a route to require pin authentication before
     * processing any request to that route. The <b>require.pin</b>
     * middleware would arrest any incoming request and return a laravel
     * signed temporary URL via the route specified in <b>pin.route</b>.
     * The User is meant to carryout a pin authentication over the
     * returned URL and the <b>require.pin</b> middleware would process
     * the previously arrested request if the authentication is successful.
     *
     * Within the config file, use the <b>pin.maxAttempts</b> and
     * the <b>pin.delayMinutes</b> to adjust the route throttling for
     * pin authentication.
     */

    public function pinRequired(Request $request, $uuid)
    {
        if ($this->hasTooManyAttempts($request)) {
            $this->_fireLockoutEvent($request);

            $data = ["message" => trans('sanctumauthstarter::pin.throttle',
                        ['seconds' => $this->_limiter()
                            ->availableIn($this->_throttleKey($request))
                        ])
                    ];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $this->incrementAttempts($request);

        if (!$request->hasValidSignature()) {
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'),
                    401, ['message' => trans('sanctumauthstarter::pin.expired_url')]);
        }

        $user = Auth::user();

        $requirePin = RequirePin::whereBelongsTo($user)
                        ->where('uuid', $uuid)
                        ->whereNull('approved_at')
                        ->whereNull('cancelled_at')
                        ->first();

        if (!isset($requirePin->id)) {
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'),
                    401, ['message' => trans('sanctumauthstarter::pin.invalid_url')]);
        }

        $validator = Validator::make($request->all(), [
            config('sanctumauthstarter.pin.input', '_pin') => ['required', 'string', new CurrentPin],
        ]);

        if ($validator->fails()) {
            $data = ['message' => (array) $validator->errors()->all()];
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        };

        if (config('sanctumauthstarter.pin.verify_sender', true)) {
            if (
                $requirePin->ip !== $this->getClientIp($request) ||
                $requirePin->device !== $request->userAgent()
            ) {
                return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'),
                        406, ['message' => trans('sanctumauthstarter::pin.unverified_sender')]);
            }
        }

        $this->updateRequest($request, unserialize(Crypt::decryptString($requirePin->payload)));

        $request = Request::create($requirePin->route_arrested, $requirePin->method, ['_uuid' => $uuid]);
        $response = Route::dispatch($request);

        $this->clearAttempts($request);

        return $response;
    }

    public function pinRequestTerminated(string $url): JsonResponse
    {
        return $this->httpJsonResponse(
            trans('sanctumauthstarter::general.fail'), 401,
            [
                'message' => trans('sanctumauthstarter::pin.terminated'),
                'url' => $url
            ]
        );
    }

    public function pinValidationURL(string $url, null|string $redirect): JsonResponse
    {
        return $this->httpJsonResponse(
            trans('sanctumauthstarter::general.success'), 200,
            [
                'message' => trans('sanctumauthstarter::pin.require_pin'),
                'url' => $url,
                'redirect' => $redirect
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
