<?php

use Illuminate\Database\Seeder;
use App\Talk;

class TalksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Talk::create([
            'user_id' => 1,
            'kind' => 1,
            'sentence1' => 'あああああああああ',
            'sentence2' => 'いいいいいいいいい',
        ]);
        Talk::create([
            'user_id' => 2,
            'kind' => 1,
            'sentence1' => 'あああああああああ',
            'sentence3' => 'ううううううううう',
        ]);
        Talk::create([
            'user_id' => 1,
            'kind' => 1,
            'sentence1' => 'あああああああああ2',
            'sentence2' => 'いいいいいいいいい2',
            'sentence3' => 'ううううううううう2',
        ]);
    }
}
