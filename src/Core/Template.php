<?php
namespace Birdmin\Core;

use Birdmin\Support\TemplateFile;
use Illuminate\Contracts\Support\Jsonable;

class Template implements Jsonable {

    protected $app;

    /**
     * The <title> element.
     * @var string
     */
    public $title;
    public $description;
    public $bodyClass;


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
    }

    /**
     * Magic method for setting attributes.
     * @param $name string
     * @param $value mixed
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
    public function head()
    {
        $output = [];
        $order = ['metas','styles','scripts'];

        foreach ($order as $var) {
            $elements = $this->$var;
            foreach ($elements as $name=>$file) {
                $output[] = $file->render($elements);
            }
        }
        return trim(join("\n",$output));
    }

    /**
     * Return the section view array.
     * @return array
     */
    public function getSections () {
        return $this->sections;
    }

    /**
     * Get/Set a template section.
     * @param $name string
     * @param null $view string
     * @return null|View
     * @throws \Exception
     */
    public function section ($name, $view=null)
    {
        if (!is_null($view)) {
            return $this->section[$name] = $view;
        }
        if (!array_key_exists($name,$this->sections)) {
            throw new \Exception ("Template section '$name' is not set.");
        }
        return $this->section[$name];
    }


    /**
     * Output this object as a JSON array or string.
     * @param bool|false $encode
     * @return array|string
     */
    public function toJson ($encode=false) {
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
    public function __toString() {
        return $this->toJson(true);
    }
}