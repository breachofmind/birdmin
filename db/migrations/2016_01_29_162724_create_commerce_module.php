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
        ModelBlueprint::get(Product::class)->createSchema();

        ModelBlueprint::get(ProductVariation::class)->createSchema();

        ModelBlueprint::get(ProductBundle::class)->createSchema();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ModelBlueprint::get(ProductBundle::class)->dropSchema();

        ModelBlueprint::get(ProductVariation::class)->dropSchema();

        ModelBlueprint::get(Product::class)->dropSchema();

    }
}
