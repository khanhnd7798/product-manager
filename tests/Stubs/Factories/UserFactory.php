<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use VCComponent\Laravel\Product\Test\Stubs\Models\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        "first_name" => $faker->firstName(),
        "last_name" => $faker->lastName(),
        "username" => $faker->userName(),
        "email" => $faker->email(),
        "verify_token" => "null"
    ];
});
