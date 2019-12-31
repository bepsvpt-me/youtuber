<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrendingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trendings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vid');
            $table->smallInteger('ranking')->unsigned();
            $table->dateTime('fetched_at');

            $table->index(['vid', 'fetched_at']);
            $table->index(['ranking', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trendings');
    }
}
