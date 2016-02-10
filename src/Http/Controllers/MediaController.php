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

    /**
     * MediaController constructor.
     * @param Template $template
     */
    public function __construct(Template $template)
    {
        parent::__construct($template, ['auth','model']);

        $this->setClass(Media::class);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $class = $this->class;

        if ($request->ajax()) {
            $models = $class::request($request, $this->user);
            $this->setTable($models,$class);

            $this->setActions([
                Button::create()->parent($class)->link('home'),
                Button::create()->parent($class)->link('view')->active(),
                Button::create()->parent($class)->link('upload'),
            ]);

            $this->setViews([
                Button::create()->parent($class)->tab('list'),
                Button::create()->parent($class)->tab('grid'),
            ]);
        }

        return $this->birdmin('cms::manage.all');
    }

    /**
     * Display the form for editing the object.
     * @param \Birdmin\Core\Model  $model
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit (Model $model, Request $request)
    {
        $class = $this->class;

        $this->setData('model',$model);

        if ($this->user->cannot('edit',$class)) {
            return $this->error("Sorry, you do not have permission to edit ".$class::plural().".");
        }
        $this->setActions([
            Button::create()->parent($model)->link('home'),
            Button::create()->parent($model)->link('view'),
            Button::create()->parent($model)->link('edit')->active(),
            Button::create()->parent($model)->action('update'),
        ]);

        return $this->birdmin('cms::media.edit');
    }


    /**
     * Display the media upload form.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->setActions([
            Button::create()->parent($this->class)->link('home'),
            Button::create()->parent($this->class)->link('view'),
            Button::create()->parent($this->class)->link('upload')->active(),
        ]);
        $this->setData('dropzone', Dropzone::create()->handler('default',cms_url('media/upload')));

        return $this->birdmin('cms::media.upload');
    }

    /**
     * Displays the media selection tool dialog.
     * GET /media/list?parent=Birdmin\Page\1
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $media = Media::request($request, $this->user,30);
        $parent = Model::str($request->input('parent'));

        return view('cms::media.select', compact('media','parent'));
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
