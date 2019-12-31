<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('channel_id')->unsigned();
            $table->string('uid')->unique();
            $table->string('name');
            $table->text('description');
            $table->bigInteger('views')->unsigned();
            $table->bigInteger('likes')->unsigned();
            $table->bigInteger('dislikes')->unsigned();
            $table->bigInteger('favorites')->unsigned();
            $table->bigInteger('comments')->unsigned();
            $table->dateTime('published_at');
            $table->dateTime('updated_at');

            $table->index(['published_at', 'channel_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
