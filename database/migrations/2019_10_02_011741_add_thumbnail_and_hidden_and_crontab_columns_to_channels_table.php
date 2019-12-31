<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThumbnailAndHiddenAndCrontabColumnsToChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->text('thumbnail')->nullable()->after('comments');
            $table->text('crontab')->nullable()->after('thumbnail');
            $table->boolean('hidden')->default(false)->index()->after('crontab');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'crontab', 'hidden']);
        });
    }
}
