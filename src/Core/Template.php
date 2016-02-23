<?php
namespace Birdmin\Core;

use Birdmin\Page;
use Birdmin\Support\TemplateFile;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Template implements Jsonable {

    protected $app;

    /**
     * The <title> element.
     * @var string
     */
    public $title;
    public $description;
    public $bodyClass;
    public $image;
    public $url;

    protected $metas = [];
    protected $styles = [];
    protected $scripts = [];
    protected $sections = [];

    protected $attributes = [];

    /**
     * Constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->url = Request::url();
    }

    /**
     * Magic method for setting attributes.
     * @param $name string
     * @param $value mixed
     * @return mixed|null
     */
    public function __set($name,$value)
    {
        return $this->setAttribute($name,$value);
    }

    /**
     * Magic method for accessing attributes.
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * Return an attribute of this template.
     * @param $name string
     * @return null|mixed
     */
    public function getAttribute($name)
    {
        return array_key_exists($name,$this->attributes) ? $this->attributes[$name] : null;
    }

    /**
     * Set an attribute of this template.
     * @param $name string
     * @param $value mixed
     * @return mixed
     */
    public function setAttribute($name, $value)
    {
        return $this->attributes[$name] = $value;

    }

    /**
     * Set multiple attributes.
     * @param array $keyval
     * @return array
     */
    public function setAttributes($keyval=[])
    {
        foreach($keyval as $key=>$value)
        {
            $this->setAttribute($key,$value);
        }
        return $this->attributes;
    }

    /**
     * Add a script file.
     * @param $name string
     * @param $src string
     * @param array $attr optional
     * @return TemplateFile
     */
    public function script ($name, $src, array $attr=[])
    {
        $attr = array_merge(['src'=>$src, 'type'=>'text/javascript'], $attr);
        $this->scripts[$name] = new TemplateFile($name, 'script', $attr);
        return $this->scripts[$name];
    }

    /**
     * Add a stylesheet.
     * @param $name string
     * @param $href string
     * @param array $attr optional
     * @return TemplateFile
     */
    public function style ($name, $href, array $attr=[])
    {
        $attr = array_merge(['href'=>$href, 'rel'=>'stylesheet', 'type'=>'text/css'], $attr);
        $this->styles[$name] = new TemplateFile($name, 'link', $attr);
        return $this->styles[$name];
    }

    /**
     * Add a meta tag.
     * @param $name string
     * @param $content string
     * @param array $attr optional
     * @return TemplateFile
     */
    public function meta ($name, $content, array $attr=[])
    {
        $attr = array_merge(['name'=>$name, 'content'=>$content], $attr);
        $this->metas[$name] = new TemplateFile($name, 'meta', $attr);
        return $this->metas[$name];
    }


    /**
     * Generate the <head> section of the template.
     * @return string
     */
    public function head($order=['metas','styles','scripts'])
    {
        $output = [];

        foreach ($order as $var) {
            $elements = $this->$var;
            foreach ($elements as $name=>$file) {
                $output[] = $file->render($elements);
            }
        }
        return trim(join("\n",$output));
    }

    /**
     * Create the meta og: tags.
     * @return void
     */
    public function openGraph()
    {
        $this->meta('og:title', $this->title);
        $this->meta('og:type', 'website');
        $this->meta('og:image', $this->image);
        $this->meta('og:url', $this->url);
    }

    /**
     * Set the template title, description and image given the model.
     * @param Model $model
     */
    public function model (Model $model)
    {
        $this->title = $model->getTitle()." - ".config('app.client_name');

        if ($model instanceof Page) {
            $this->description = Str::limit(strip_tags($model->content),160);
        } else {
            $this->description = $model->excerpt ?: Str::limit(strip_tags($model->description),160);
        }
        if ($image = $model->getImage()) {
            $this->image = $image->url(null,false);
        } else {
            $this->image = url(config('app.client_logo'));
        }
    }

    /**
     * Output this object as a JSON array or string.
     * @param bool|false $encode
     * @return array|string
     */
    public function toJson ($encode=false)
    {
        $out = [
            'title' => $this->title,
            'description' => $this->description,
            'body' => $this->bodyClass,
        ];
        return $encode ? json_encode($out) : $out;
    }

    /**
     * To string method.
     * @return array|string
     */
    public function __toString()
    {
        return $this->toJson(true);
    }
}