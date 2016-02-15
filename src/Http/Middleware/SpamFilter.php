<?php

namespace Birdmin\Http\Middleware;

use Closure;
use Birdmin\Lead;
use Illuminate\Http\Request;

class SpamFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request['valid'] = 1;

        if (!empty($request->input('hpv')) && !empty($request->input('hpt'))) {
            $request['valid'] = 0;
        }

        $email = $request->input('email');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $request['valid'] = 0;
        }

        return $next($request);
    }
}
