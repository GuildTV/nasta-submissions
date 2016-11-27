<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFileConstraintsAddVideoDuration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_constraints', function (Blueprint $table) {
            $table->renameColumn('extensions', 'mimetypes');
            $table->integer('video_duration')->nullable()->after('extensions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_constraints', function (Blueprint $table) {
            $table->renameColumn('mimetypes', 'extensions');
            $table->dropColumn('video_duration');
        });
    }
}
