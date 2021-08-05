<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use VCComponent\Laravel\Product\Test\Stubs\Models\Attribute;

$factory->define(Attribute::class, function (Faker $faker) {
    return [
        "name" => $faker->word(),
        "type" => $faker->slug(),
        "kind" => "products"
    ];
});
