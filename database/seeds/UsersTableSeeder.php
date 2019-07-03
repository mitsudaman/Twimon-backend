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
        $price = DB::table('users')->max('serial_number');;
        $price ++;
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000001',
            'name' => 'がっちょ',
            'title' => 'ツイットモンスター',
            'feature1' => 'タイプ',
            'feature1_content' => 'じめん',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'img_src' => 'gattyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000002',
            'name' => 'masahikoっちょ',
            'title' => 'ツイットモンスター',
            'feature1' => 'タイプ',
            'feature1_content' => '無',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'img_src' => 'masahikottyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000003',
            'name' => '枝っちょ',
            'title' => 'ツイットモンスター',
            'feature1' => 'タイプ',
            'feature1_content' => 'ノーマル',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'img_src' => 'edamamettyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000004',
            'name' => 'むっちょ',
            'title' => 'ツイットモンスター',
            'feature1' => 'タイプ',
            'feature1_content' => '草',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'img_src' => 'mutottyo.png'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000005',
            'name' => 'おさむっちょ',
            'title' => 'ツイットモンスター',
            'feature1' => 'タイプ',
            'feature1_content' => 'きんにく',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'img_src' => 'osamuttyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000006',
            'name' => 'やめたっちょ',
            'title' => 'ツイットモンスター',
            'feature1' => 'タイプ',
            'feature1_content' => 'めがね',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'img_src' => 'yametattyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000007',
            'name' => 'じどはんっちょ',
            'title' => 'ツイットモンスター',
            'feature1' => 'タイプ',
            'feature1_content' => 'きかい',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'img_src' => 'jidohanttyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000008',
            'name' => 'ミツダマっちょ',
            'title' => 'ツイットモンスター',
            'feature1' => 'タイプ',
            'feature1_content' => 'みず',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'img_src' => 'mitsudamattyo.jpg'
        ]);
        factory('App\User', 50)->create();
        // //特定のデータを追加
        // User::create([
        //     'name' => 'ツイモン',
        //     'title' => 'ツイットモンスター',
        //     'feature1' => 'タイプ',
        //     'feature1_content' => 'はがね',
        //     'feature2' => 'レベル',
        //     'feature2_content' => '99',
        // ]);
    }
}