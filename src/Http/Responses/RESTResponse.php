<?php
namespace Birdmin\Http\Responses;

use Birdmin\Support\RESTResponseObject;
use Illuminate\Http\Response;
use Illuminate\Validation\Validator;

/**
 * This is a special type of response that the API uses.
 * See Birdmin\Support\RESTResponseObject for the format.
 *
 * Class RESTResponse
 * @package Birdmin\Http\Responses
 */
class RESTResponse extends Response {

    /**
     * The response object.
     * @var RESTResponseObject
     */
    protected $content;

    public function __construct($content='', $status=200, $headers=[])
    {
        $this->original = $content;

        parent::__construct($content,$status,$headers);

        $this->header('Content-Type', 'application/json');

        $this->content->update();
    }

    /**
     * Alias for quickly returning a failed response.
     * @param $status
     * @param $message
     * @param int $code
     * @return mixed
     */
    public static function failed($message,$status,$code=0)
    {
        return static::create(null,$status)->error($message,$code);
    }

    /**
     * Alias for displaying validation errors.
     * @param Validator $validator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function failedValidation(Validator $validator)
    {
        $response = static::create(null,406);
        foreach ($validator->messages()->all() as $msg) {
            $response->error($msg,0);
        }
        return $response;
    }

    /**
     * Log an error message.
     * @param $message string
     * @param int $code
     * @return $this
     */
    public function error($message,$code=0)
    {
        $this->content->error($message,$code);
        return $this;
    }

    /**
     * Add some metadata to the response.
     * @param $key string
     * @param $value mixed
     * @return $this
     */
    public function meta($key,$value)
    {
        $this->content->meta($key,$value);
        return $this;
    }

    /**
     * Create the REST Response object.
     * @param mixed $content
     * @return void
     */
    public function setContent($content)
    {
        $this->content = new RESTResponseObject($this, $content);
    }


    /**
     * Return the status text for the current code.
     * @return string
     */
    public function getStatusText()
    {
        return self::$statusTexts[$this->getStatusCode()];
    }

    /**
     * Gets the current response content.
     * @return string Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns the content array.
     * @return array
     */
    public function getJson()
    {
        return $this->content->toArray();
    }

    /**
     * Sends content for the current web response.
     * @return Response
     */
    public function sendContent()
    {
        echo $this->getContent();

        return $this;
    }
}