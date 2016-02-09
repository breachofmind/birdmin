<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInputTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inputs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid',32);
            $table->integer('active')->unsigned()->default(1);
            $table->integer('priority')->default(1);
            $table->string('object',200)->index();
            $table->string('name',100);
            $table->string('label',200);
            $table->text('options');
            $table->string('type', 50)->default("text");
            $table->text('description');
            $table->integer('in_table')->unsigned()->default(0);
            $table->integer('required')->unsigned()->default(0);
            $table->integer('unique')->unsigned()->default(0);
            $table->text('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('inputs');
    }
}
