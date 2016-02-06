<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid',32);
            $table->string('title',500);
            $table->dateTime('published_at');
            $table->string('status', 100);
            $table->string('type', 100);
            $table->text('excerpt');
            $table->string('slug',250);
            $table->text('content');
            $table->integer('user_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts');
    }
}
