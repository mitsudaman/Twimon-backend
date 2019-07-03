<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    static $number = 9;
    return [
        'serial_number' => $number++,
        'account_id' => $faker->word,
        'name' => $faker->name,
        'title' => $faker->sentence(2),
        'feature1' => $faker->word,
        'feature1_content' => $faker->sentence(3),
        'feature2' => $faker->word,
        'feature2_content' => $faker->sentence(3),
        'description' => $faker->text,
        'img_src' => 'mitsudamattyo.jpg',
        // 'email' => $faker->unique()->safeEmail,
        // 'email_verified_at' => now(),
        // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        // 'remember_token' => Str::random(10),
    ];
});
