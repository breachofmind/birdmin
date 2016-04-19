<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Birdmin\Support\ModelBluePrint;
use Birdmin\Location;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Location::blueprint()->createSchema();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Location::blueprint()->dropSchema();
    }
}
