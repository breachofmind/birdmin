<?php

namespace Birdmin\Http\Controllers\REST;

use Birdmin\Http\Responses\RESTResponse;
use Illuminate\Http\Request;
use Birdmin\Core\Controller;
use Birdmin\Model;
use Illuminate\Support\Facades\Session;

class ModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response("Birdmin API v1",200);
    }

    public function session()
    {
        return response(Session::getId(),200);
    }

    /**
     * Return all records for the given model.
     * GET /model
     *
     * @param $slug string
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function fetchAll ($slug, Request $request)
    {
        $class = $request->Model->getClass();

        // If the model does not allow public viewing, check credentials.
        if (! $request->Model->getBlueprint('public')) {
            if ($killed = $this->isNotAuthorized($request,'view')) {
                return $killed;
            }
        }

        $pagination = $class::request($request, $this->user, $request->input('limit'));

        return RESTResponse::create($pagination);
    }


    /**
     * Return a single model object.
     * GET /model/id
     *
     * @param $slug string
     * @param $id int
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function fetch ($slug,$id,Request $request)
    {
        $class = $request->Model->getClass();

        // If the model does not allow public viewing, check credentials.
        if (! $request->Model->getBlueprint('public')) {
            if ($killed = $this->isNotAuthorized($request,'view')) {
                return $killed;
            }
        }

        return RESTResponse::create($request->Model);
    }


    /**
     * Create a new model.
     * POST /model
     *
     * @param $slug string
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create ($slug, Request $request)
    {
        $class = $request->Model->getClass();

        if ($killed = $this->isNotAuthorized($request,'create')) {
            return $killed;
        }

        $input = $request->all();
        $model = new $class($input);

        $validator = $model->validate($input);

        if ($validator->fails()) {
            return RESTResponse::failedValidation($validator);
        }
        if ($model->save()) {
            return RESTResponse::create($model, 200);
        }

        return RESTResponse::failed("Error saving ".$class::singular(), 500);
    }

    /**
     * Update an existing model.
     * PUT /model/id
     *
     * @param $slug string
     * @param $id int
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update ($slug,$id, Request $request)
    {
        if ($killed = $this->isNotAuthorized($request,'edit')) {
            return $killed;
        }

        $input = $request->all();
        $model = $request->Model;

        $validator = $model->validate($input);

        if ($validator->fails()) {
            return RESTResponse::failedValidation($validator);
        }
        if ($model->update($input)) {
            return RESTResponse::create($model, 200);
        }

        return RESTResponse::failed("Error updating {$model->objectName}", 500);
    }


    /**
     * Remove an existing object.
     * DELETE /model/id
     *
     * @param $slug string
     * @param $id int
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy ($slug,$id, Request $request)
    {
        if ($killed = $this->isNotAuthorized($request,'delete')) {
            return $killed;
        }

        $model = $request->Model;
        if ($model->delete()) {
            return RESTResponse::create($model,200);
        }

        return RESTResponse::failed("Error deleting {$model->objectName}", 500);
    }


    /**
     * Does this model require a user to be logged in and have permission?
     * @param Request $request
     * @param string $ability
     * @return mixed
     */
    protected function isNotAuthorized(Request $request, $ability='view')
    {
        $class = $request->Model->getClass();

        if (! $this->user || $this->user->cannot($ability, $class)) {
            return RESTResponse::failed("You are not permitted to $ability ".$class::plural(), 401);
        }

        // Checked out.
        return false;
    }

}
