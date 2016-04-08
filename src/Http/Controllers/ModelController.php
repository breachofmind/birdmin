<?php

namespace Birdmin\Http\Controllers;

use Birdmin\Components\Button;
use Birdmin\Components\ButtonGroup;
use Birdmin\Core\Template;
use Birdmin\Http\Responses\CMSResponse;
use Birdmin\Support\Table;
use Birdmin\Core\Controller;
use Birdmin\Core\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModelController extends Controller
{

    /**
     * Constructor.
     * User is required to be signed in and
     * all requests should be prefixed with a model class (/models/...)
     */
    public function __construct(Template $template)
    {
        parent::__construct($template, ['auth','model']);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $class = $this->setClass($request->model->getClass());

        if ($request->ajax()) {
            $models = $class::request($request, $this->user);
            $this->setTable ($models,$class);

            $this->setActions([
                Button::create()->parent($class)->link('home'),
                Button::create()->parent($class)->link('view')->active(),
                Button::create()->parent($class)->link('create'),
            ]);
            $this->setViews([
                Button::create()->parent($class)->link('list')->active(),
                Button::create()->parent($class)->link('tree'),
            ]);
        }

        return $this->birdmin('cms::manage.all');
    }



    /**
     * Display the tree view.
     * @usage /models/tree
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function tree (Request $request)
    {
        $class = $this->setClass($request->model->getClass());

        if ($request->ajax()) {
            $this->setData('roots',$class::roots()->get());

            $this->setActions([
                Button::create()->parent($class)->link('home'),
                Button::create()->parent($class)->link('view')->active(),
                Button::create()->parent($class)->link('create'),
            ]);
            $this->setViews([
                Button::create()->parent($class)->link('list'),
                Button::create()->parent($class)->link('tree')->active(),
            ]);
        }

        return $this->birdmin('cms::manage.tree');
    }

    /**
     * Display the form for editing the object.
     * @param \Birdmin\Core\Model  $model
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit (Model $model, Request $request)
    {
        $class = $this->setClass($model->getClass());

        $this->setData('model',$model);
        if ($this->user->cannot('edit',$model)) {
            return $this->error("Sorry, you do not have permission to edit ".$class::plural().".");
        }
        $this->setActions([
            Button::create()->parent($model)->link('home'),
            Button::create()->parent($model)->link('view'),
            Button::create()->parent($model)->link('edit')->active(),
            Button::create()->parent($model)->action('update'),
        ]);

        return $this->birdmin('cms::manage.edit');
    }




    /**
     * Display the form for creating the object.
     * @usage /models/create
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function create (Request $request)
    {
        $class = $this->setClass($request->model->getClass());
        $model = $this->setData('model',new $class);
        $model->inputs();

        $this->setActions([
            Button::create()->parent($model)->link('home'),
            Button::create()->parent($model)->link('view'),
            Button::create()->parent($model)->link('create')->active(),
            Button::create()->parent($model)->action('save'),
        ]);

        $this->setViews([
            Button::create()->parent($class)->link('list'),
            Button::create()->parent($class)->link('tree'),
        ]);

        return $this->birdmin('cms::manage.create');
    }


    /**
     * Create a new model from the request.
     * @usage /models/create
     * @param Request $request
     * @return static
     */
    public function store (Request $request)
    {
        $class = $this->setClass($request->model->getClass());
        $input = $request->all();
        $model = $this->setData('model',new $class($input));

        $validator = $model->validate($input);

        if ($validator->fails()) {
            return CMSResponse::failed($validator->messages()->all());
        }

        if ($model->save()) {
            return CMSResponse::saved($model);
        }
        // Something else went wrong...
        return CMSResponse::failed($model);
    }


    /**
     * Update the specified resource in storage.
     * @usage /models/edit/1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update (Model $model, Request $request)
    {
        $class = $this->setClass($model->getClass());
        $input = $request->all();

        $validator = $model->validate($input);

        if ($validator->fails()) {
            return CMSResponse::failed($validator->errors()->all());
        }

        if ($model->update($input)) {
            return CMSResponse::updated($model);
        }
        // Something else went wrong...
        return CMSResponse::failed($model);
    }


    /**
     * Remove the specified resource from storage.
     * @usage /models/edit/1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy (Model $model)
    {
        //TODO
    }
}
