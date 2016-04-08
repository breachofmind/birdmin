<?php
namespace Birdmin\Support;

use Birdmin\Core\Model;
use Birdmin\Http\Requests\Request;
use Birdmin\Http\Responses\RESTResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;

class RESTResponseObject implements Arrayable, \JsonSerializable
{
    /**
     * The response object.
     * @var RESTResponse
     */
    protected $response;

    /**
     * The original request.
     * @var Request
     */
    protected $request;

    /**
     * The data property.
     * @var string
     */
    protected $data;

    /**
     * Error array, if any.
     * @var array
     */
    protected $_errors = [];

    /**
     * The links property. (contains external refs or pagination links)
     * @var array
     */
    protected $_links = [];

    /**
     * The meta property.
     * @var array
     */
    protected $_meta = [
        'statusCode' => 200,
        'statusText' => 'OK',
        'method' => 'GET',
    ];

    /**
     * RESTResponseObject constructor.
     * @param RESTResponse $response
     * @param string $data
     */
    public function __construct(RESTResponse $response, $data='')
    {
        $this->response = $response;
        $this->request = app('request');

        if ($data instanceof LengthAwarePaginator)
        {
            $this->data = $data->items();
            $this->meta('paging.total', $data->total());
            $this->meta('paging.per_page', $data->perPage());
            $this->meta('paging.current_page', $data->currentPage());
            $this->meta('paging.last_page', $data->lastPage());
            $this->meta('paging.from', $data->firstItem());
            $this->meta('paging.to', $data->lastItem());
            $this->link('prev_page', $data->previousPageUrl());
            $this->link('next_page', $data->nextPageUrl());

        } else {

            $this->data = $data;
        }

        $this->meta('method', $this->request->getMethod());
    }

    /**
     * Updates any metadata from the response.
     * @return $this
     */
    public function update ()
    {
        $this->meta('statusCode', $this->response->getStatusCode());
        $this->meta('statusText', $this->response->getStatusText());

        return $this;
    }

    /**
     * Create a meta key.
     * @param $key
     * @param $value
     * @return $this
     */
    public function meta($key,$value)
    {
        array_set($this->_meta, $key,$value);
        return $this;
    }

    /**
     * Create a link.
     * @param $key
     * @param $value
     * @return $this
     */
    public function link($key,$value)
    {
        array_set($this->_links, $key,$value);
        return $this;
    }

    /**
     * Get the count of errors.
     * @return int
     */
    public function errorCount()
    {
        return count($this->_errors);
    }

    /**
     * Log an error.
     * @param $message string
     * @param int $code
     * @return $this
     */
    public function error($message, $code=0)
    {
        $this->_errors[] = ['detail' => $message, 'code' => $code];
        return $this;
    }

    /**
     * Convert this object to an array to be encoded.
     * @return array
     */
    public function toArray()
    {
        $out = [];
        $out['meta'] = $this->_meta;
        if (! empty($this->_links)) {
            $out['links'] = $this->_links;
        }
        if (! empty($this->_errors)) {
            $out['errors'] = $this->_errors;
        } else {
            $out['data'] = $this->getDataArray();
        }
        return $out;
    }

    /**
     * Serializes object for json_encode.
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }


    /**
     * Encode to a JSON string.
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray());
    }


    /**
     * Get the data object as an array or string.
     * @return array
     */
    public function getDataArray()
    {
        if ($this->data instanceof Arrayable) {
            return $this->data->toArray();
        }

        return $this->getData();
    }

    /**
     * Return the protected data property.
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}