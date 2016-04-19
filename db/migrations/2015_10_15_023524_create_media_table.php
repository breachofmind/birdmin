<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Birdmin\Media;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Media::blueprint()->createSchema(function($table,$fields) {
            $fields['etag']->index();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Media::blueprint()->dropSchema();
    }
}
