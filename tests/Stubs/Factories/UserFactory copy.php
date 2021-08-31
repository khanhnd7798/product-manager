<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use VCComponent\Laravel\User\Entities\User;

$factory->define(User::class, function (Faker $faker) {
    $email = $faker->email;
    return [
        'email'        => $email,
        'password'     => $faker->password,
        'username'     => $faker->userName,
        'verify_token' => Hash::make($email),
    ];
});
