<?php

namespace Birdmin\Http\Controllers\REST;

use Birdmin\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use Birdmin\Core\Controller;
use Birdmin\Model;

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

    /**
     * Return all records for the given model.
     * GET /model
     *
     * @param $model string
     * @param ApiRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getAll ($model, ApiRequest $request)
    {
        $class = $this->isValid ($model);
        return $class::all();
    }

    /**
     * Return a single model object.
     * GET /model/id
     *
     * @param $model string
     * @param $id int
     * @param ApiRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function get ($model,$id, ApiRequest $request)
    {
        $class = $this->isValid ($model);
        return $class::find($id);
    }


    /**
     * Create a new model.
     * POST /model
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create ($model, ApiRequest $request)
    {
        $class = $this->isValid ($model);

        return TRUE; //TODO
    }

    /**
     * Update an existing model.
     * PUT /model/id
     *
     * @param $model string
     * @param $id int
     * @param ApiRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update ($model,$id, ApiRequest $request)
    {
        $class = $this->isValid ($model);
        return TRUE; //TODO
    }
    /**
     * Remove an existing object.
     * DELETE /model/id
     *
     * @param $model string
     * @param $id int
     * @param ApiRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy ($model, $id, ApiRequest $request)
    {
        $class = $this->isValid ($model);
        return TRUE; //TODO
    }

    protected function isValid ($model)
    {
        $class = is_model($model);
        if (!$class) {
            abort(400,"Bad Request.");
        }
        return $class;
    }
}
