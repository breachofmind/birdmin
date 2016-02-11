<?php

if (!function_exists('birdmin_path')) {

    /**
     * Get the path to the Birdmin folder.
     *
     * @param  string  $path
     * @return string
     */
    function birdmin_path($path = '')
    {
        return base_path('cms'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }

    /**
     * Create a url for CMS use.
     * @param string $path
     * @return string
     */
    function cms_url ($path="")
    {
        $backend = config('app.cms_uri');
        return empty($path) ? "/".$backend : "/".$backend."/".$path;
    }

    /**
     * Check if the given string is the name of a model class.
     * Used if passed as a URL.
     * @param $modelName
     * @return bool|string
     */
    function is_model($modelName)
    {
        $className = 'Birdmin\\' . ucwords($modelName);
        if (class_exists($className) && is_subclass_of($className, 'Birdmin\\Core\\Model')) {
            // Is a model.
            return $className;
        }
        // Not a model.
        return false;
    }

    /**
     * Returns a multi-dimensional array from a CSV file.
     * I got this from PHP.net. Handy!
     *
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     * @return array
     */
    function csv_to_array ($filename, $delimiter=',', $enclosure='"')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return array();
        }
        $header = null;
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, null, $delimiter, $enclosure)) !== false) {
                if (!$header) $header = $row;
                else $data[] = array_combine($header,$row);
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Convert an array back to a csv file.
     *
     * @param array $array
     * @param string $filename - output
     * @param string $delimiter - default is comma
     * @param boolean $headers - is first row the headers or is array key=>val?
     * @throws Exception
     * @return boolean
     */
    function array_to_csv ($array, $filename="output.csv", $delimiter=",", $headers=true)
    {
        if (!is_array($array)) {
            throw new \Exception('No array given');
        }
        if (!$headers) {
            array_unshift ($array, array_keys($array[0]));
        }
        $handle = fopen($filename,'w');
        foreach ($array as $row) {
            fputcsv ($handle,$row,$delimiter);
        }
        return fclose($handle);
    }

    /**
     * Convert an array of key=>value into html attributes or element..
     * @param $array array
     * @param null|string $tag, like img or input
     * @return string
     */
    function attributize ($array, $tag=null)
    {
        $string = [];
        foreach ($array as $attr=>$value) {
            if (true===$value) {
                $string[] = $attr; // just pass the attribute name
                continue;
            }
            if (empty($value)) {
                continue;
            }
            $string[] = "$attr=\"$value\"";
        }
        $attributes = join(" ",$string);
        return $tag ? "<$tag $attributes/>" : $attributes;
    }

    /**
     * Lookup the mimetype in the config.
     * @param $extension
     * @return null
     */
    function lookup_mimetype ($extension)
    {
        $map = config('media.map');
        if (!array_key_exists($extension,$map)) {
            return null;
        }
        return $map[$extension];
    }

    /**
     * Decodes the given special yaml file into a php array.
     * @param string $file path
     * @return array
     */
    function decode_model_yaml ($file)
    {
        try {
            $data = (yaml_parse_file($file));
        } catch (\Exception $exception) {
            return $exception;
        }

        $inputs = [];
        $priority = 0;
        $fields = $data['fields'];
        $object = $data['model'];
        foreach ($fields as $nametype => $labels) {
            list ($name, $type) = explode("|", $nametype);
            $options = "";
            $value = "";
            if (array_key_exists('options', $labels)) {
                list ($label, $description) = $labels['label'];
                $options = $labels['options'];
            } else {
                list ($label, $description) = $labels;
            }
            if (array_key_exists('value', $labels)) {
                $value = $labels['value'];
            }
            $in_table = (int)in_array($name, $data['in_table']);
            $unique = (int)in_array($name, $data['unique']);
            $required = (int)in_array($name, $data['required']);

            // Create the input array.
            $inputs[] = compact('priority', 'name', 'type', 'label',
                'description', 'options', 'object', 'required',
                'in_table', 'unique', 'value');

            $priority++;
        }
        $data['fields'] = $inputs;
        return $data;
    }


    /**
     * Return a related model name, given the field string (such as user_id)
     * @param $model Model
     * @param $field string
     * @return null|Model
     */
    function get_model_from_field ($model,$field)
    {
        $method = str_replace("_id","",$field);
        if (method_exists($model, $method)) {
            $object = $model->$method()->first();
            return $object;
        }
        return null;
    }

    /**
     * Similar to sprintf, only you can use an associative array to replace the formatted string.
     * Example: "My name is {name}" and the array: array('name'=>'Mike');
     * @param string $format
     * @param array|object $values
     * @return string
     */
    function stringf ($format, $values)
    {
        $values = (array)$values;
        $matches = array();
        preg_match_all ("/\{([^}]+)\}/", $format, $matches);
        list ($str,$key) = $matches;
        if (empty($str) || empty($values)) {
            return $format;
        }

        // Perform the replacement on the format string.
        $i=0;
        while ($i<count($str)) {
            if (array_key_exists($key[$i],$values)) {
                $format = str_replace ($str[$i],$values[$key[$i]],$format);
            }
            $i++;
        }
        return $format; // Result with replacements.
    }

    /**
     * Check if an object adheres to a contract.
     * @param $object object|string
     * @param $interface string interface name
     * @return bool
     */
    function has_contract($object,$interface)
    {
        $interfaces = class_implements($object);
        return isset($interfaces[$interface]);
    }
}
