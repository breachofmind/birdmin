<?php
namespace Birdmin\Contracts;

use Birdmin\Core\Component;

/**
 * Option components have a view for the user to input data,
 * and the data is stored in the database for use in it's parent component.
 * Used by the CMS.
 *
 * Interface OptionComponent
 * @package Birdmin\Contracts
 */
interface OptionComponent {

    /**
     * Set the parent component.
     * @param Component $component
     * @return mixed
     */
    public function component(Component $component);

}