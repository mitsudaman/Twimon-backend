<?php

use Illuminate\Database\Seeder;
use App\User;
use Faker\Generator as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\User', 10)->create();
        //特定のデータを追加
        User::create([
            'name' => 'ツイモン',
            'title' => 'ツイットモンスター',
            'feature1' => 'タイプ',
            'feature1_content' => 'はがね',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'email' => 'test1@test.com',
            'password' => Hash::make('testtest')
        ]);
    }
}

// 'name' => $faker->name,
// 'feature1' => $faker->word,
// 'feature1_content' => $faker->sentence(3),
// 'feature2' => $faker->word,
// 'feature2_content' => $faker->sentence(3),
// 'description' => $faker->text,
// 'email' => $faker->unique()->safeEmail,
// 'email_verified_at' => now(),
// 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
// 'remember_token' => Str::random(10),