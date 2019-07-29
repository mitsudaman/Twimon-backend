<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('serial_number');
            $table->string('account_id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('feature1')->nullable();
            $table->string('feature1_content')->nullable();
            $table->string('feature2')->nullable();
            $table->string('feature2_content')->nullable();
            $table->string('description')->nullable();
            $table->string('upload_img_url')->nullable();
            $table->string('sns_img_url')->nullable();
            $table->boolean('sns_img_use_flg')->default(1);
            $table->string('ogp_img_url')->nullable();
            $table->boolean('hall_of_fame_flg')->default(0);
            $table->boolean('legend_flg')->default(0);

            // $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable();
            // $table->string('password');
            // $table->string('provider');
            // $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
