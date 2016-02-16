<?php
namespace Birdmin\Support;


use Illuminate\Contracts\Support\Renderable;

class TemplateFile implements Renderable {

    private $templates = [
        'link'      => '<link %s/>',
        'script'    => '<script %s></script>',
        'meta'      => '<meta %s/>'
    ];

    private $fileSrc = [
        'link' => 'href',
        'script' => 'src'
    ];

    protected $name;
    protected $dependencies = [];
    protected $attr = [];
    protected $src;

    protected $rendered = false;

    /**
     * Constructor.
     * @param $name string
     * @param $element string - link|script|meta|og:title, etc
     * @param array $attr - additional attributes
     */
    public function __construct($name, $element, array $attr=[])
    {
        $this->name     = $name;
        $this->element  = strtolower($element);
        $this->attr     = $attr;
        $this->src      = array_key_exists($element,$this->fileSrc) ? $attr[$this->fileSrc[$element]] : null;
    }


    /**
     * Associate dependencies with this file, such as jquery.
     * Dependencies are rendered first.
     * @param $dependencies string
     * @return $this
     */
    public function dependsOn ($dependencies)
    {
        $dependencies = func_get_args();
        foreach ($dependencies as $name) {
            if (is_string($name) && !in_array($name,$this->dependencies)) {
                $this->dependencies[] = $name;
            }
        }
        return $this;
    }


    /**
     * Return the names of the dependencies for this file.
     * @return array
     */
    public function getDependencies ()
    {
        return $this->dependencies;
    }


    /**
     * Check if the file has been rendered already.
     * @return bool
     */
    public function isRendered ()
    {
        return $this->rendered;
    }


    /**
     * Render the dependencies the html for this file.
     * @param array $files
     * @return string
     * @throws \Exception
     */
    public function render ($files=[])
    {
        $output = [];
        // Resolve dependencies first.
        foreach ($this->dependencies as $name) {
            if (!array_key_exists($name,$files)) {
                throw new \Exception("Dependency '$name' missing for '{$this->name}'");
            }
            $output[] = $files[$name]->render($files);
        }
        if ($this->rendered) {
            return trim(join("\n",$output));
        }
        // Cache busting
        if ($this->src && file_exists(base_path($this->src))) {
            $this->attr[$this->fileSrc[$this->element]].="?t=".filemtime(base_path($this->src));
        }
        $this->rendered = true;
        $output[] = sprintf($this->templates[$this->element], attributize($this->attr));

        return trim(join("\n",$output));
    }

}