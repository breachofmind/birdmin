<?php

namespace Birdmin\Http\Requests;
use Illuminate\Http\Request as BaseRequest;

class ApiRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->filters = $this->parseFilter($this->input('filter'));
        return true; // TODO - Currently, API is publicly exposed.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Parses the string filter to use in QueryBuilder.
     * @param $string
     * @return array
     */
    protected function parseFilter ($string)
    {
        $operators = ["==","<=",">=","!=",">","<","~"];

        // Separate the rules. TODO - these are 'AND' rules only.
        $rules = explode(";",$string);
        $parsed = array();

        // Match operators.
        foreach ($rules as $rule) {
            foreach ($operators as $op) {
                if (strstr($rule,$op)) {
                    list($property,$value) = explode($op,$rule);
                    $parsed[] = [$property,$op,$value];
                    break;
                }
            }
        }
        return $parsed;
    }
}
