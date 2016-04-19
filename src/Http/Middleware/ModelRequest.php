<?php

namespace Birdmin\Http\Middleware;

use Birdmin\Core\Application;
use Birdmin\Http\Responses\RESTResponse;
use Birdmin\Support\ModelBlueprint;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Birdmin\Core\Model;

/**
 * This middleware will check if the request parameters contain a model and/or ID.
 * IF they do, the middleware will attach the model class or object to the request.
 * Otherwise, will terminate with response.
 *
 * Class ModelRequest
 * @package Birdmin\Http\Middleware
 */
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
    public function handle(Request $request, Closure $next)
    {
        $modelIndex = Application::context(Application::CXT_API) ? 3 : 2;
        $idIndex = Application::context(Application::CXT_API) ? $modelIndex+1 : $modelIndex+2;

        // Check for the model slug. Throw 404 if not found.
        $modelSegment = $request->segment($modelIndex);
        $idSegment = $request->segment($idIndex);
        $blueprint = ModelBlueprint::slug($modelSegment);

        if (! $blueprint)
        {
            $message = "Class for '$modelSegment' does not exist";

            return Application::context(Application::CXT_API)
                ? RESTResponse::failed($message, 404)
                : response($message, 404);
        }

        $class = $blueprint->getClass();

        // Check if the model ID was given.
        if ($idSegment)
        {
            $model = $class::find($idSegment);

            if (! $model)
            {
                $message = "Model '{$modelSegment}/{$idSegment}' does not exist";

                return Application::context(Application::CXT_API)
                    ? RESTResponse::failed($message, 404)
                    : response($message, 404);
            }

            $request->Model = $model;

            return $next($request);
        }

        // Otherwise, return the static model class.
        $request->Model = new $class;

        return $next($request);
    }
}
