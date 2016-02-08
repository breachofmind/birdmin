<?php

namespace Birdmin\Core;

use Birdmin\Http\Responses\CMSResponse;
use Birdmin\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Birdmin\Components\ButtonGroup;
use Birdmin\Support\Table;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Data to pass into template.
     * @var array
     */
    protected $data = [];

    /**
     * Template instance.
     * @var Template
     */
    protected $template;

    /**
     * Authorized user.
     * @var User
     */
    protected $user;

    /**
     * The currently requested class or parent class.
     * @var string
     */
    protected $class;

    /**
     * Controller constructor.
     * @param $template
     */
    public function __construct(Template $template, $middlewares=[])
    {
        $this->template = $template;

        $template->title = 'Birdmin';
        $template->meta  ('viewport', 'width=device-width, initial-scale=1.0');
        $template->style ('cms',      '/cms/public/static/cms.css');
        $template->script('cms_lib',  '/cms/public/static/cms.lib.js');
        $template->script('cms_src',  '/cms/public/static/cms.src.js')->dependsOn('cms_lib');

        foreach ($middlewares as $middleware) {
            $this->middleware($middleware);
        }

        $this->user = Auth::user();
    }

    /**
     * Standard birdmin handler.
     * @param string $view
     * @param array $with
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    protected function birdmin ($view)
    {
        $this->data['template'] = $this->template;
        $this->data['user'] = $this->user;

        if (Request::ajax())
        {
            $this->data['view'] = view($view, $this->data)->render();
            $this->data['template'] = $this->data['template']->toJson();

            return response()->json($this->data);
        }

        return view('cms::app', $this->data);
    }


    /**
     * Response with error message.
     * @param $message
     * @return $this|static
     */
    protected function error ($message)
    {
        if (Request::ajax()) {
            return CMSResponse::failed([$message]);
        }
        return view('cms::app', compact('user'))->withErrors([$message]);
    }


    protected function setClass($name)
    {
        return $this->class = $this->setData('class',$name);
    }

    protected function setTable($models,$class)
    {
        $this->setData('table', Table::create($models,$class)->toJson());
    }

    protected function setActions($array=[])
    {
        return $this->setData('actions', ButtonGroup::build($array));
    }

    protected function setViews($array=[])
    {
        return $this->setData('views', ButtonGroup::build($array));
    }

    protected function setData($key,$value)
    {
        return $this->data[$key] = $value;
    }
    
}
