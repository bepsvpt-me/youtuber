<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('video_id')->unsigned();
            $table->bigInteger('views')->unsigned();
            $table->bigInteger('likes')->unsigned();
            $table->bigInteger('dislikes')->unsigned();
            $table->bigInteger('favorites')->unsigned();
            $table->bigInteger('comments')->unsigned();
            $table->dateTime('fetched_at');

            $table->index(['video_id', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_statistics');
    }
}
