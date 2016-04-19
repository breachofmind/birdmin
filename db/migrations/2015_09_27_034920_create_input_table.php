<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Birdmin\Input;

class CreateInputTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Input::blueprint()->createSchema(function($table,$fields)
        {
            $fields['type']->default(Input::TEXT);
            $fields['active']->default(1);
            $fields['object']->index();
            $fields['in_table']->default(0);
            $fields['required']->default(0);
            $fields['unique']->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Input::blueprint()->dropSchema();
    }
}
