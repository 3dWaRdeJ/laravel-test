<?php

use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;


$fakerUA = \Faker\Factory::create('uk_UA');
/** @var Factory $factory */
$factory->define(App\Position::class, function (Faker $faker) use ($fakerUA) {

    $user = User::getRandom();

    return [
        'name' => $faker->unique()->jobTitle,
        'admin_create_id' => $user->id,
        'admin_update_id' => $user->id
    ];
});
