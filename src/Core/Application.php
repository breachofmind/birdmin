<?php
namespace Birdmin\Core;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

class Application extends LaravelApplication {

    const VERSION = "0.0.2";

    // Context or channels.
    const CXT_SITE = 1;
    const CXT_CMS  = 2;
    const CXT_API  = 4;

    const ENV_LOCAL = 'local';
    const ENV_DEV   = 'development';
    const ENV_PROD  = 'production';

    protected $namespace = 'Birdmin\\';

    /**
     * Get the path to the application "birdmin" directory.
     * @return string
     */
    public function path()
    {
        return $this->basePath.DS.'cms'.DS.'src';
    }



    /**
     * Get the path to the database directory.
     * Birdmin has it's own separate directory for installation purposes.
     * @return string
     */
    public function databasePath()
    {
        return $this->basePath.DS.'cms'.DS.'db';
    }

    /**
     * Get the path to the language files.
     * @return string
     */
    public function langPath()
    {
        return $this->basePath.DS.'cms'.DS.'assets'.DS.'lang';
    }

    /**
     * Return the context type or match against the given type.
     * @return int|boolean
     */
    public static function context($is=null)
    {
        if (is_int($is)) {
            return static::context() === $is;
        }
        // Determine the type of request.
        switch (Request::segment(1))
        {
            case config('app.cms_uri') : return static::CXT_CMS;
            case "api" :                 return static::CXT_API;
            default:                     return static::CXT_SITE;
        }
    }
}