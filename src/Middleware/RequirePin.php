<?php

namespace Ikechukwukalu\Sanctumauthstarter\Middleware;

use Closure;
use Illuminate\Http\Request;
use Ikechukwukalu\Sanctumauthstarter\Controllers\PinController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;

use Ikechukwukalu\Sanctumauthstarter\Models\RequirePin as RequirePinModel;

class RequirePin
{

    public function handle(Request $request, Closure $next)
    {
        $pinController = new PinController();

        if (!Auth::check()) {
            return $pinController->pinRequestTerminated();
        }

        $user = Auth::user();

        if ($request->has(config('sanctumauthstarter.pin.param', '_uuid'))) {
            $param = config('sanctumauthstarter.pin.param', '_uuid');
            $requirePin = RequirePinModel::whereBelongsTo($user)
                            ->where('route_arrested', $request->path())
                            ->where('uuid', $request->{$param})
                            ->whereNull('approved_at')
                            ->whereNull('cancelled_at')
                            ->first();

            if (isset($requirePin->id)) {
                $requirePin->approved_at = now();
                $requirePin->save();

                return $next($request);
            }

        }

        RequirePinModel::whereBelongsTo($user)
            ->whereNull('approved_at')
            ->whereNull('cancelled_at')
            ->update(['cancelled_at' => now()]);

        $redirect_to = config('sanctumauthstarter.pin.redirect_to', null);
        $uuid = (string) Str::uuid();
        $expires_at = now()->addSeconds(
            config('sanctumauthstarter.pin.duration', null));

        $pin_validation_url = URL::temporarySignedRoute(
            config('sanctumauthstarter.pin.route', 'require_pin'),
            $expires_at, ['uuid' => $uuid]);

        RequirePinModel::create([
            "user_id" => $user->id,
            "uuid" => $uuid,
            "ip" => $pinController->getClientIp($request),
            "device" => $request->userAgent(),
            "method" => $request->method(),
            "route_arrested" => $request->path(),
            "payload" => Crypt::encryptString(serialize($request->all())),
            "redirect_to" => $redirect_to,
            "pin_validation_url" => $pin_validation_url,
            "expires_at" => $expires_at
        ]);

        return $pinController->pinValidationURL($pin_validation_url, $redirect_to);
    }

}
