<?php

namespace Birdmin\Http\Middleware;

use Closure;

class SpamFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input = $request->input();

        if (!empty($request->input('hpv')) && !empty($request->input('hpt'))) {
            return $this->hasSpam($request);
        }

        $email = $request->input('email');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->hasSpam($request);
        }

        return $next($request);
    }

    protected function hasSpam($request)
    {
        if ($request->ajax()) {
            return response('Unauthorized.', 401);
        }
        redirect($request->input('redirect'));
    }
}
