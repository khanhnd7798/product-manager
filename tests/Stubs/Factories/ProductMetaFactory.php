<?php

use Faker\Generator as Faker;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductMeta;

$factory->define(ProductMeta::class, function (Faker $faker) {
    return [
        'key' => $faker->word()
    ];
});
