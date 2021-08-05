<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use VCComponent\Laravel\Product\Test\Stubs\Models\Variant;

$factory->define(Variant::class, function (Faker $faker) {
    return [
        "label" => $faker->name(),
        "price" => rand(10000, 999999),
        "type"  => "variant_type",
        "product_id" => rand(1, 10),
        "status" => 1,
    ];
});
