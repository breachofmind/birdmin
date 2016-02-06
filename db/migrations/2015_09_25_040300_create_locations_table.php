<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid',32);
            $table->string('name',500);
            $table->text('description');
            $table->text('directions');
            $table->string('address', 500);
            $table->string('address_2', 500);
            $table->string('city', 300);
            $table->string('state',2);
            $table->string('zip', 10);
            $table->string('county',100);
            $table->string('country',3);
            $table->decimal('lat',8,6);
            $table->decimal('lng',8,6);
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
        Schema::drop('locations');
    }
}
