<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Birdmin\Support\ModelBlueprint;
use Birdmin\Page;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Page::blueprint()->createSchema();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Page::blueprint()->dropSchema();
    }
}
