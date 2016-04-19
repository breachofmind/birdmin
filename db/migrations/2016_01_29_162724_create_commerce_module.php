<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Birdmin\Support\ModelBlueprint;
use Birdmin\Product;
use Birdmin\ProductBundle;
use Birdmin\ProductVariation;

class CreateCommerceModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Product::blueprint()->createSchema();

        ProductVariation::blueprint()->createSchema();

        ProductBundle::blueprint()->createSchema();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ProductBundle::blueprint()->dropSchema();

        ProductVariation::blueprint()->dropSchema();

        Product::blueprint()->dropSchema();
    }
}
