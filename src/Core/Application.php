<?php
namespace Birdmin\Core;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

class Application extends LaravelApplication {

    // Context or channels.
    const CXT_SITE = 1;
    const CXT_CMS  = 2;
    const CXT_API  = 4;

    const ENV_LOCAL = 'local';
    const ENV_DEV = 'development';
    const ENV_PROD = 'production';

    protected $namespace = 'Birdmin\\';

    /**
     * Get the path to the application "birdmin" directory.
     * @return string
     */
    public function path()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'cms'.DIRECTORY_SEPARATOR.'src';
    }



    /**
     * Get the path to the database directory.
     * Birdmin has it's own separate directory for installation purposes.
     * @return string
     */
    public function databasePath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'cms'.DIRECTORY_SEPARATOR.'db';
    }
}