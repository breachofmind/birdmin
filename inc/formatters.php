<?php
namespace Birdmin\Formatters;

if (!function_exists('Birdmin\Formatters\edit_model_link'))
{
    function edit_model_link ($model,$field) {
        $url = $model->editUrl();
        return "<a href='$url' brd-link>".$model->getAttribute($field)."</a>";
    }

    function id_to_model ($model,$field) {
        $object = \get_model_from_field ($model, $field);
        if ($object) {
            $url = $object->editUrl();
            $title = $object->getTitle();
            return "<a href='$url' brd-link>$title</a>";
        }
        // Didn't find it. Sorry.
        return $model->$field;
    }

    function preview ($model,$field) {
        $url = $model->editUrl();
        return "<a href='$url' brd-link>".$model->img('sm','preview-image')."</a>";
    }

    function url ($model,$field) {
        $url = $model->url();
        return "<a href='$url' title='$url' target='_blank'><i class='lnr-select2'></i></a>";
    }

    function id_to_user ($model,$field) {
        $object = \get_model_from_field ($model, $field);
        $url = $object->editUrl();
        $label = $object->fullName();
        return "<a href='$url' brd-link>$label</a>";
    }

    function bulk ($model,$field) {
        return "<input ng-model='bulk' type='checkbox' value='{$model->id}' name='_bulk'/>";
    }

    function date ($model,$field) {
        return $model->$field;
    }

    function swatch($model,$field)
    {
        $color = $model->getAttribute($field);
        return "<div class='swatch' style='background-color:#$color;'></div>";
    }

    /**
     * Check if the given object is a duplicate of another object.
     * @param $model
     * @param $field
     * @return string
     */
    function isDuplicate($model,$field) {
        if ($duplicate = $model->isDuplicate()) {
            return $duplicate->id;
        }
        return '-';
    }

    function roles ($model,$field) {
        $roles = $model->roles;
        return join(", ",$roles->pluck('name')->toArray());
    }
}