<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Birdmin\Support\ModelBlueprint;
use Birdmin\Category;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        ModelBlueprint::get(Category::class)->createSchema();
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        ModelBlueprint::get(Category::class)->dropSchema();
    }
}
