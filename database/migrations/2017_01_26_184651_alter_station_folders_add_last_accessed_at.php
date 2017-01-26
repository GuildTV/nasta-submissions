<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStationFoldersAddLastAccessedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_folders', function (Blueprint $table) {
            $table->timestamp('last_accessed_at')->after('folder_name')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('station_folders', function (Blueprint $table) {
            $table->dropColumn('last_accessed_at');
        });
    }
}
