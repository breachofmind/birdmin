<?php

use Illuminate\Database\Seeder;

use Illuminate\Database\Eloquent\Model;

class PostSeeder extends Seeder
{
    /**
     * Installs some posts to work with.
     *
     * @return void
     */
    public function run()
    {
        factory(Birdmin\Post::class, 20)->create();
        factory(Birdmin\Page::class, 6)->create();
    }
}
