<?php
namespace Birdmin\Components;

use Birdmin\Contracts\Hierarchical;
use Birdmin\Core\Component;
use Birdmin\Core\Model;
use Symfony\Component\HttpFoundation\Request;

class ButtonGroup extends Component
{
    protected $name = "Button Group";
    protected $view = "cms::components.button-group";

    protected $buttons = [];

    protected $element;


    /**
     * A handy starter for creating a button group with buttons.
     * @param array $buttons
     * @return ButtonGroup
     */
    public static function build(array $buttons=[])
    {
        $group = new static();
        foreach ($buttons as $button) {
            $group->add($button);
        }
        return $group;
    }

    /**
     * Sets the button group parent HTML element. ie, div|li|nav
     * @param $name string
     * @return $this
     */
    public function element($name)
    {
        $this->element = $name;
        return $this;
    }

    /**
     * Add a button object.
     * @param Button $button
     * @return $this
     */
    public function add(Button $button)
    {
        if (! $button->canRender) {
            return $this;
        }
        $this->buttons[] = $button;
        return $this;
    }

    /**
     * Return the number of buttons set.
     * @return int
     */
    public function count()
    {
        return count($this->buttons);
    }

    /**
     * Return the button array.
     * @return array
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * Turn the button objects into HTML and pass to the data var.
     * @return array
     */
    public function prepare()
    {
        $this->with($this->toArray());
    }

    /**
     * Return an array of properties, which will also be in json_encode.
     * @return array
     */
    public function toArray()
    {
        return [
            'element' => $this->element,
            'buttons'=>$this->buttons,
            'attributes' => $this->attributes->toArray(),
            'count' => $this->count()
        ];
    }
}