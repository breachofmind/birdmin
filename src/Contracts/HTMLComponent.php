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
     * Set the parent model.
     * @param Model $model
     * @return mixed
     */
    public function parent(Model $model);

    /**
     * Set the html node.
     * @param \simple_html_dom_node $node
     * @return mixed
     */
    public function node(\simple_html_dom_node $node);

}