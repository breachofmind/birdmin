<?php
use Birdmin\User;
use Birdmin\Post;
use Birdmin\Page;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(Post::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->words(7,true),
        'excerpt' => $faker->sentence(),
        'content' => $faker->paragraph(),
        'slug' => $faker->slug(),
        'published_at' => $faker->dateTimeBetween('-60 days', 'now'),
        'type' => 'post',
        'status'=> $faker->randomElement(['publish','draft','revision']),
        'user_id'=>1
    ];
});
$factory->define(Page::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->words(2,true),
        'content' => $faker->paragraph(),
        'slug' => $faker->slug(),
        'type' => 'page',
        'status'=> $faker->randomElement(['publish','draft']),
        'user_id'=>1
    ];
});
