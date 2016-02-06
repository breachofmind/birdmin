<?php
namespace Birdmin\Components;

use Birdmin\Contracts\Hierarchical;
use Birdmin\Core\Model;
use Birdmin\Core\Component;

class ButtonComponent extends Component
{
    protected $name = "Button";

    protected $view = "cms::components.button";

    /**
     * The attributes of the button.
     * @var array
     */
    protected $attributes = [
        'href' => '#'
    ];

    /**
     * Attributes for setupAction to use.
     * @var array
     */
    protected $parentAttributes = [];

    /**
     * The parent model or class, determining how this button functions.
     * @var Model|string class
     */
    protected $parent;

    /**
     * The icon to use, if any.
     * @var string
     */
    protected $icon;

    /**
     * The label for the button.
     * @var string
     */
    protected $label;


    /**
     * Common action handles, labels, urls and icons.
     * @var array
     */
    protected $actions = [
        //Handle     => Label               URL               Icon
        'home'       => [null,             '',                'home3'],
        'navigation' => ['{navigation}',   '{p}',             '{icon}'],
        'view'       => ['View {P}',       '{p}',             '{icon}'],
        'create'     => ['Create New {S}', '{p}/create',      'plus-circle'],
        'save'       => ['Save {S}',       '{p}/create',      'checkmark-circle'],
        'update'     => ['Update {S}',     '{p}/edit/{id}',   'checkmark-circle'],
        'upload'     => ['Upload {P}',     '{p}/create',      'upload'],
        'delete'     => ['Delete {S}',     '{p}/destroy/{id}','trash2'],
        'edit'       => ['Edit {S}',       '{p}/edit/{id}',   'pencil3'],

        'list'       => [null,             '{p}',             'list'],
        'grid'       => [null,             '{p}?grid=true',   'grid'],
        'tree'       => [null,             '{p}/tree',        'site-map'],
        'media'      => [null,             '{p}/media/{id}',  'picture2'],
        'assigned'   => [null,             '{p}/assigned',    'paperclip'],
    ];

    /**
     * Button classes to use for specific actions.
     * @var array
     */
    protected $actionClass = [
        'home'     => 'visited',
        'view'     => 'visited',
        'create'   => 'success',
        'save'     => 'success',
        'update'   => 'success',
        'upload'   => 'success',
        'delete'   => 'alert',
        'edit'     => 'visited',
    ];

    /**
     * When using an action, check if it needs to adhere to a contract.
     * @var array
     */
    protected $contracts = [
        'tree' => Hierarchical::class
    ];

    /**
     * Set a label.
     * @param $name string label
     * @return $this
     */
    public function label($name)
    {
        $this->label = $name;
        return $this;
    }

    /**
     * Set the icon name. Make null to disable.
     * @param $name string|null
     * @return $this
     */
    public function icon($name)
    {
        $this->icon = $name;
        return $this;
    }

    /**
     * Apply the active class to this button.
     * @return ButtonComponent
     */
    public function active()
    {
        return $this->classes('active');
    }


    /**
     * Assign a parent object to this button.
     * @param $object
     * @return $this
     */
    public function parent ($object)
    {
        $this->parent = $object;

        $this->parentAttributes = [
            's' => $object::singular(),
            'p' => $object::plural(),
            'S' => $object::singular(true),
            'P' => $object::plural(true),
            'id' => $object instanceof Model ? $object->id : null,
            'title' => $object instanceof Model ? $object->titleField : null,
            'icon' => $object::getIcon(),
            'navigation' => $object::getLabel('navigation')
        ];
        return $this;
    }


    /**
     * Create a brd-link out of this button.
     * @param null $action
     * @return ButtonComponent
     */
    public function link($action=null)
    {
        $this->setupAction($action);
        return $this->attribute('brd-link');
    }

    /**
     * Create a brd-submit action out of this button.
     * @param null $action
     * @return ButtonComponent
     */
    public function action($action=null)
    {
        $object = $this->parent;
        $form = $object->uid ? $object->uid : $object::singular(true);
        $this->setupAction($action);
        return $this->attribute('brd-submit', $form."Form");
    }

    /**
     * Use an action handle to quickly set up this button with common properties.
     * @param $name string action name
     * @return $this|null
     */
    protected function setupAction($name)
    {
        if (!$name || !array_key_exists($name,$this->actions)) {
            return null;
        }
        list ($label,$url,$icon) = $this->actions[$name];
        $this->label = stringf($label, $this->parentAttributes);
        $this->attribute('href', cms_url( stringf($url, $this->parentAttributes) ));
        $this->icon  = stringf($icon, $this->parentAttributes);

        // Check if the specific action has a CSS class it normally uses.
        if (isset($this->actionClass[$name])) {
            $this->classes( $this->actionClass[$name] );
        }
        // If the parent doesn't adhere to the contract, don't render it.
        if (isset($this->contracts[$name])) {
            $this->canRender = has_contract($this->parent, $this->contracts[$name]);
        }

        return $this;
    }

    /**
     * Add this button to a button group.
     * @param ButtonGroupComponent $component
     * @return $this
     */
    public function addTo(ButtonGroupComponent $component)
    {
        $component->add($this);
        return $this;
    }



    /**
     * Get the button label.
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get the icon.
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Return an array of properties, which will also be in json_encode.
     * @return array
     */
    public function toArray()
    {
        return [
            'attributes' => $this->attributes,
            'label' => $this->label,
            'icon' => $this->icon,
            'href' => $this->getAttribute('href'),
        ];
    }

    /**
     * Prepare the button to be rendered.
     * @return void
     */
    public function prepare()
    {
        $data = $this->toArray();
        $data['attributes'] = attributize($data['attributes']);

        $this->with($data);
    }

}