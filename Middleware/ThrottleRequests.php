<?php

namespace App\Http\Api\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests as Throttle;

class ThrottleRequests extends Throttle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  float|int  $decayMinutes
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $maxAttempts = config('api.throttle.max_attempts', $maxAttempts);

        $decayMinutes = config('api.throttle.decay_minutes', $decayMinutes);

        parent::handle($request, $next, $maxAttempts, $decayMinutes);
    }
}
