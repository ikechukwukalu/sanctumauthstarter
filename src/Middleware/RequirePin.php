<?php

namespace Ikechukwukalu\Sanctumauthstarter\Middleware;

use Closure;
use Illuminate\Http\Request;
use Ikechukwukalu\Sanctumauthstarter\Controllers\PinController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

use Ikechukwukalu\Sanctumauthstarter\Models\RequirePin as RequirePinModel;

class RequirePin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($request->has('_uuid')) {
            $requirePin = RequirePinModel::whereBelongsTo($user)
                            ->where('route_arrested', $request->path())
                            ->where('uuid', $request->_uuid)
                            ->whereNull('approved_at')
                            ->whereNull('cancelled_at')
                            ->first();

            if (isset($requirePin->uuid)) {
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

        $pinController = new PinController();

        RequirePinModel::create([
            "user_id" => $user->id,
            "uuid" => $uuid,
            "ip" => $pinController->getClientIp($request),
            "device" => $request->userAgent(),
            "method" => $request->method(),
            "route_arrested" => $request->path(),
            "payload" => serialize($request->all()),
            "redirect_to" => $redirect_to,
            "pin_validation_url" => $pin_validation_url,
            "expires_at" => $expires_at
        ]);

        if (isset($redirect_to)) {
            redirect(config('sanctumauthstarter.pin.redirect_to'));
        }

        return $pinController->pinValidationURL($pin_validation_url);
    }

}
