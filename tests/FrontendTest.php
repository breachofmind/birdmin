<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FrontendTest extends TestCase
{
    public function test_homepage()
    {
        $this->visit('/')->see('Test');
    }

    public function test_login()
    {
        $backend = config('app.cms_uri');
        $this->visit("/$backend")->see('User name');
    }
}
