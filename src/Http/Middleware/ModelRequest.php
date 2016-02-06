<?php

namespace Birdmin\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Config;
use Birdmin\Core\Model;

class ModelRequest
{

    protected $except;

    protected $user;
    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->user = $auth->user();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $segment = $request->segment(2);
        if (!array_key_exists($segment, Model::$map)) {
            return response("Class for '$segment' does not exist.", 404);
        }
        $class = Model::$map[$segment];

        $request->model_class = $class;

        return $next($request);
    }
}
