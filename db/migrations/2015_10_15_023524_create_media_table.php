<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid',32);
            $table->string('title', 200);
            $table->string('file_name',250);
            $table->integer('file_size');
            $table->string('file_type',250);
            $table->string('alt_text', 200);
            $table->text('caption');
            $table->string('category',250);
            $table->text('metadata');
            $table->string('etag',100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::drop('media');
    }
}
