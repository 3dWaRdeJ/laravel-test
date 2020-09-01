<?php

use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$fakerUA = \Faker\Factory::create('uk_UA');
/** @var Factory $factory */
$factory->define(App\Employee::class, function (Faker $faker) use ($fakerUA){

    $user = User::getRandom();

    $phoneNumber = preg_replace('/^.*(\d{9})$/', '+380${1}', $faker->e164PhoneNumber);

    return [
        'full_name' => $fakerUA->name,
        'salary' => sprintf('%6d.%02d', rand(0, 500000), rand(0, 99)),
        'start_date' => $faker->date('Y-m-d'),
        'phone' => $phoneNumber,
        'email' => $faker->unique()->safeEmail,
        'admin_create_id' => $user->id,
        'admin_update_id' => $user->id
    ];
});
