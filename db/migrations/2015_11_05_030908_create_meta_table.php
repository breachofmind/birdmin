<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetaTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('meta', function (Blueprint $table) {
            $table->increments('id');
            $table->string('object', 300)->index();
            $table->string('key', 300)->index();
            $table->text('value');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::drop('meta');
    }
}
