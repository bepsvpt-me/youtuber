<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityAndDeletedAndHiddenColumnsToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->smallInteger('priority')->unsigned()->default(50000)->after('comments');
            $table->boolean('hidden')->default(false)->after('priority');
            $table->boolean('deleted')->default(false)->after('updated_at');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->index(['deleted', 'priority', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['deleted', 'priority', 'published_at']);
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['priority', 'hidden', 'deleted']);
        });
    }
}
