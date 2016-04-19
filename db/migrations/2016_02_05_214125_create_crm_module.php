<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Birdmin\Lead;
use Birdmin\Support\ModelBlueprint;

class CreateCrmModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Lead::blueprint()->createSchema();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Lead::blueprint()->dropSchema();
    }
}
