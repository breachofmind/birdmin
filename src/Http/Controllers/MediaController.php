<?php

namespace Birdmin\Http\Controllers;

use Birdmin\Collections\MediaCollection;
use Birdmin\Core\Controller;
use Birdmin\Core\Template;
use Birdmin\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Birdmin\Support\Table;
use Birdmin\Components\ButtonComponent;
use Birdmin\Components\ButtonGroupComponent;

class MediaController extends Controller
{

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
                ButtonComponent::create()->parent($class)->link('upload'),
            ]);

            $views = ButtonGroupComponent::build([
                ButtonComponent::create()->parent($class)->link('list')->active(),
                ButtonComponent::create()->parent($class)->link('grid'),
            ]);
        }

        $this->data = compact('class','table','actions','views');

        return $this->birdmin('cms::manage.all');
    }

    /**
     * Display the media upload form.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $class = Media::class;
        $this->data = compact('class');

        return $this->birdmin('cms::media.upload');
    }

    /**
     * POST and Upload the media.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $objects = new MediaCollection();
        foreach ($request->files->all() as $uploadedFile) {
            $objects[] = Media::upload($uploadedFile);
        }
        return $objects;
    }

}
