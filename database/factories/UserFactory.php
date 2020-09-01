<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => 'admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('admin'), // secret
        'api_token' => str_random(20),
        'remember_token' => str_random(10),
    ];
});
