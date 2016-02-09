<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('uid',32);
            $table->string('first_name',300);
            $table->string('last_name',300);
            $table->string('email',250)->index();
            $table->string('affiliation',500);
            $table->string('phone', 100);
            $table->string('source',200);
            $table->string('interest',500);
            $table->string('session_id', 100);
            $table->string('type', 200);
            $table->integer('valid')->unsigned()->default(1);
            $table->text('comments');
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('leads');
    }
}
