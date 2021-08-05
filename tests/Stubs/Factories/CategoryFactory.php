<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use VCComponent\Laravel\Category\Entities\Category;

$factory->define(Category::class, function (Faker $faker) {
    return [
        "name" => $faker->word(),
        "slug" => $faker->slug(),
        "type" => "products"
    ];
});
