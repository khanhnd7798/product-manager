<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductSchemaRule;

$factory->define(ProductSchemaRule::class, function (Faker $faker) {
    return [
        'name' => $faker->word(2)
    ];
});
