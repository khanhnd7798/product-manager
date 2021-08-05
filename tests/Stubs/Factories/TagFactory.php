<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use VCComponent\Laravel\Product\Test\Stubs\Models\Tag;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        "name" => $faker->word(),
        "slug" => $faker->slug(),
    ];
});
