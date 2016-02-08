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
interface ModuleComponent {

    /**
     * Set the parent model.
     * @param Model $model
     * @return mixed
     */
    public function parent(Model $model);

}