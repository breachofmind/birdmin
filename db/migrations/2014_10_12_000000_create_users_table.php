<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Birdmin\User;
use Birdmin\Support\ModelBluePrint;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        User::blueprint()->createSchema(function(Blueprint $table,$fields)
        {
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        User::blueprint()->dropSchema();
    }
}
