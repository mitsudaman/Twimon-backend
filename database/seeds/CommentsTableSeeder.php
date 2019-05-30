<?php
// php artisan db:seed --class=CommentsTableSeeder
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 3) as $num) {
            DB::table('comments')->insert([
                'user_id' => 1,
                // 'title' => "サンプルタスク {$num}",
                'content' => "あああああああああああああ",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
