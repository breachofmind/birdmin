<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommerceModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->references('id')->on('categories')->default(0);
            $table->integer('bundle_id')->unsigned()->references('id')->on('product_bundles')->default(0);
            $table->string('uid',32);
            $table->string('name',500);
            $table->string('brand',500);
            $table->string('sku',200);
            $table->string('status', 100);
            $table->string('type', 100);
            $table->string('slug',250)->index();
            $table->string('excerpt',250);
            $table->text('description');
            $table->text('attributes');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_variations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('product_id')->unsigned()->index();
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('uid',32);
            $table->string('name',500);
            $table->string('sku',200);
            $table->string('status', 100);
            $table->text('description');
            $table->text('attributes');
            $table->string('color',10);
            $table->string('color_name',200);
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('product_bundles', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('uid',32);
            $table->string('name',500);
            $table->string('brand',400);
            $table->string('slug',200);
            $table->string('status', 100);
            $table->string('excerpt',500);
            $table->string('website',255);
            $table->text('description');
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
        Schema::drop('product_bundles');
        Schema::drop('product_variations');
        Schema::drop('products');
    }
}
