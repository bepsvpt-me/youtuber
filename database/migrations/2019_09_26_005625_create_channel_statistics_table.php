<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('channel_id')->unsigned();
            $table->bigInteger('subscribers')->unsigned();
            $table->bigInteger('views')->unsigned();
            $table->bigInteger('videos')->unsigned();
            $table->bigInteger('comments')->unsigned();
            $table->dateTime('fetched_at');

            $table->index(['channel_id', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_statistics');
    }
}
