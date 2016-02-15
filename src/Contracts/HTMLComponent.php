<?php
namespace Birdmin\Contracts;

use Birdmin\Core\Model;

/**
 * Component acts as a separate view of a specific model data segment.
 * Used by the CMS.
 *
 * Interface ModuleComponent
 * @package Birdmin\Contracts
 */
interface HTMLComponent {

    /**
     * Set up the node and model.
     * @param Model $model
     * @param \simple_html_dom_node $node
     * @return mixed
     */
    public function setup(Model $model, \simple_html_dom_node $node);

}