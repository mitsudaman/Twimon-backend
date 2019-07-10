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
            'sentence3' => 'ううううううううう',
        ]);
    }
}
