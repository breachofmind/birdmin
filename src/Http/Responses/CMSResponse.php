<?php
namespace Birdmin\Http\Responses;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * This is a special type of response that the CMS uses.
 * The frontend angular.js will expect the response to be a certain format.
 *
 * Class AngularResponse
 * @package Birdmin\Http\Responses
 */
class CMSResponse extends Response {

    /**
     * A general failure response.
     * @param array $errors
     * @return static
     */
    public static function failed($errors=[])
    {
        if (empty($errors)) {
            $errors[] = "Error... Something went wrong.";
        }
        return new static([
            'success' => false,
            'errors' => $errors,
            'messages' => []
        ]);
    }

    /**
     * A model was successfully saved.
     * @param $model
     * @return static
     */
    public static function saved($model)
    {
        return new static([
            'success' => true,
            'model' => $model->toArray(),
            'redirect' => $model->editUrl(),
            'messages' => [$model::singular(true)." Saved."],
            'errors' => []
        ]);
    }

    /**
     * A model was successfully updated.
     * @param $model
     * @return static
     */
    public static function updated($model)
    {
        return new static([
            'success' => true,
            'model' => $model->toArray(),
            'messages' => [$model::singular(true)." Updated."],
            'errors' => []
        ]);
    }

}