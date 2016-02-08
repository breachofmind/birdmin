<?php

namespace Birdmin\Http\Controllers;

use Birdmin\Collections\MediaCollection;
use Birdmin\Components\Dropzone;
use Birdmin\Core\Controller;
use Birdmin\Core\Template;
use Birdmin\Media;
use Illuminate\Http\Request;
use Birdmin\Components\Button;
use Birdmin\Core\Model;

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
        $class = $this->setClass($request->model_class);

        if ($request->ajax()) {
            $models = $class::request($request, $this->user);
            $this->setTable($models,$class);

            $this->setActions([
                Button::create()->parent($class)->link('home'),
                Button::create()->parent($class)->link('view')->active(),
                Button::create()->parent($class)->link('upload'),
            ]);

            $this->setViews([
                Button::create()->parent($class)->tab('list')->active(),
                Button::create()->parent($class)->tab('grid'),
            ]);
        }

        return $this->birdmin('cms::manage.all');
    }

    /**
     * Display the media upload form.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $class = $this->setClass(Media::class);

        $this->setActions([
            Button::create()->parent($class)->link('home'),
            Button::create()->parent($class)->link('view'),
            Button::create()->parent($class)->link('upload')->active(),
        ]);
        $this->setData('dropzone', Dropzone::create()->handler('default',cms_url('media/upload')));

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
        foreach ((array)$request->input('relate') as $parentObjectName) {
            $objects->attach($parentObjectName);
        }
        return $objects;
    }

}
