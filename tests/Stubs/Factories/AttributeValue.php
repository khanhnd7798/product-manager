<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use VCComponent\Laravel\Product\Test\Stubs\Models\AttributeValue;

$factory->define(AttributeValue::class, function (Faker $faker) {
    return [
        "attribute_id" => rand(1, 5),
        "label" => $faker->word(2),
        "value" => $faker->slug(),
    ];
});
