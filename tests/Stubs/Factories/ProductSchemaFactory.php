<?php

use Faker\Generator as Faker;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductSchema;

$factory->define(ProductSchema::class, function (Faker $faker) {
    return [
        'name'           => 'phone',
        'label'          => 'Phone',
        'product_type'   => 'products',
        'schema_type_id' => 1,
        'schema_rule_id' => 5,
    ];
});

$factory->state(ProductSchema::class, 'sim', function () {
    return [
        'product_type' => 'sim'
    ];
});
