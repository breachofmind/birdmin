<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Birdmin\Support\ModelBlueprint;
use Birdmin\ProductBundle;

class AddBundleRedirectColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_bundles', function(Blueprint $table) {
            $table->string('redirect');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_bundles', function(Blueprint $table) {
            $table->dropColumn('redirect');
        });
    }
}
