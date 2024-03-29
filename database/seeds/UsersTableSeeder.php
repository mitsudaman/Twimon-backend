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
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000001',
            'name' => 'がっちょ',
            'nickname' => 'gattyo',
            'title' => 'ツイットモンスター',
            'type1' => 'じめん',
            'type2' => 'フェアリー',
            'feature1' => 'タイプ',
            'feature1_content' => 'じめん',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'sns_img_url' => 'https://twimon.s3-ap-northeast-1.amazonaws.com/seeds/gattyo/gattyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000002',
            'name' => 'masahikoっちょ',
            'nickname' => 'amasahiko2',
            'title' => 'ツイットモンスター',
            'type1' => 'ノーマル',
            'type2' => 'どく',
            'feature1' => 'タイプ',
            'feature1_content' => '無',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'sns_img_url' => 'https://twimon.s3-ap-northeast-1.amazonaws.com/seeds/gattyo/masahikottyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000003',
            'name' => '枝っちょ',
            'nickname' => 'eda',
            'title' => 'ツイットモンスター',
            'type1' => 'くさ',
            'type2' => 'むし',
            'feature1' => 'タイプ',
            'feature1_content' => 'ノーマル',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'sns_img_url' => 'https://twimon.s3-ap-northeast-1.amazonaws.com/seeds/gattyo/edamamettyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000004',
            'name' => 'むっちょ',
            'nickname' => 'muttyo',
            'title' => 'ツイットモンスター',
            'type1' => 'ゴースト',
            'type2' => 'でんき',
            'feature1' => 'タイプ',
            'feature1_content' => '草',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'sns_img_url' => 'https://twimon.s3-ap-northeast-1.amazonaws.com/seeds/gattyo/mutottyo.png'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000005',
            'name' => 'おさむっちょ',
            'nickname' => 'osamuttyo',
            'title' => 'ツイットモンスター',
            'type1' => 'エスパー',
            'type2' => 'いわ',
            'feature1' => 'タイプ',
            'feature1_content' => 'きんにく',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'sns_img_url' => 'https://twimon.s3-ap-northeast-1.amazonaws.com/seeds/gattyo/osamuttyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000006',
            'name' => 'やめたっちょ',
            'nickname' => 'yametattyo',
            'title' => 'ツイットモンスター',
            'type1' => 'はがね',
            'type2' => 'こおり',
            'feature1' => 'タイプ',
            'feature1_content' => 'めがね',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'sns_img_url' => 'https://twimon.s3-ap-northeast-1.amazonaws.com/seeds/gattyo/yametattyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000007',
            'name' => 'じどはんっちょ',
            'nickname' => 'auto',
            'title' => 'ツイットモンスター',
            'type1' => 'かくとう',
            'type2' => 'ひこう',
            'feature1' => 'タイプ',
            'feature1_content' => 'きかい',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'sns_img_url' => 'https://twimon.s3-ap-northeast-1.amazonaws.com/seeds/gattyo/jidohanttyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000008',
            'name' => 'ミツダマっちょ',
            'nickname' => 'mitudama',
            'title' => 'ツイットモンスター',
            'type1' => 'ドラゴン',
            'type2' => 'みず',
            'feature1' => 'タイプ',
            'feature1_content' => 'みず',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'description1' => 'ふくおかに せいそくする うぇぶの ぷろぐらまー。',
            'description2' => 'ぶらっくな かいしゃから すぐいなくなる。2びょう',
            'description3' => 'かんに 1000もじの コードを かくことができる。',
            'sns_img_url' => 'https://twimon.s3-ap-northeast-1.amazonaws.com/seeds/gattyo/mitsudamattyo.jpg'
        ]);
        User::create([
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'account_id' => '000009',
            'name' => 'ブラックパラディン',
            'nickname' => 'paradin',
            'title' => 'ツイットモンスター',
            'type1' => 'あく',
            'type2' => 'ほのお',
            'feature1' => 'タイプ',
            'feature1_content' => 'まほう',
            'feature2' => 'レベル',
            'feature2_content' => '99',
            'sns_img_url' => 'https://twimon.s3-ap-northeast-1.amazonaws.com/seeds/gattyo/blackparadin.jpg'
        ]);
        // factory('App\User', 50)->create();
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