<?php

namespace Birdmin\Http\Controllers;

use Birdmin\Components\ButtonComponent;
use Birdmin\Components\ButtonGroupComponent;
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
     * Key value store of data.
     * This data is available in both JSON and HTML responses.
     * @var array
     */
    protected $data = [];

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
        $class = $request->model_class;

        if ($request->ajax()) {
            $models = $class::request($request, $this->user);
            $table = Table::create($models,$class)->toJson();

            $actions = ButtonGroupComponent::build([
                ButtonComponent::create()->parent($class)->link('home'),
                ButtonComponent::create()->parent($class)->link('view')->active(),
                ButtonComponent::create()->parent($class)->link('create'),
            ]);

            $views = ButtonGroupComponent::build([
                ButtonComponent::create()->parent($class)->link('list')->active(),
                ButtonComponent::create()->parent($class)->link('tree'),
            ]);
        }

        $this->data = compact('class','table','actions','views');

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
        $class = $request->model_class;

        if ($request->ajax()) {
            $roots = $class::roots()->get();

            $actions = ButtonGroupComponent::build([
                ButtonComponent::create()->parent($class)->link('home'),
                ButtonComponent::create()->parent($class)->link('view')->active(),
                ButtonComponent::create()->parent($class)->link('create'),
            ]);

            $views = ButtonGroupComponent::build([
                ButtonComponent::create()->parent($class)->link('list'),
                ButtonComponent::create()->parent($class)->link('tree')->active(),
            ]);
        }

        $this->data = compact('class','actions','views','roots');

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
        $class = $request->model_class;

        if ($this->user->cannot('edit',$class)) {
            return $this->error("Sorry, you do not have permission to edit ".$class::plural().".");
        }

        $actions = ButtonGroupComponent::build([
            ButtonComponent::create()->parent($model)->link('home'),
            ButtonComponent::create()->parent($model)->link('view'),
            ButtonComponent::create()->parent($model)->link('edit')->active(),
            ButtonComponent::create()->parent($model)->action('update'),
        ]);

        $views = ButtonGroupComponent::build([
            ButtonComponent::create()->parent($class)->link('list'),
            ButtonComponent::create()->parent($class)->link('tree'),
        ]);

        $this->data = compact('class','actions','views','model');

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
        $class = $request->model_class;
        $model = new $class;
        $model->inputs();

        $actions = ButtonGroupComponent::build([
            ButtonComponent::create()->parent($model)->link('home'),
            ButtonComponent::create()->parent($model)->link('view'),
            ButtonComponent::create()->parent($model)->link('create')->active(),
            ButtonComponent::create()->parent($model)->action('save'),
        ]);

        $views = ButtonGroupComponent::build([
            ButtonComponent::create()->parent($class)->link('list'),
            ButtonComponent::create()->parent($class)->link('tree'),
        ]);

        $this->data = compact('class','actions','views','model');

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
        $class = $request->model_class;
        $input = $request->all();
        $model = new $class($input);

        $validator = $model->validate($input);

        if ($validator->fails()) {
            return CMSResponse::failed($validator->errors()->all());
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
        $class = get_class($model);
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
        //
    }
}
