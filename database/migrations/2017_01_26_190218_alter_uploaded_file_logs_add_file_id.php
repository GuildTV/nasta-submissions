<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUploadedFileLogsAddFileId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uploaded_file_logs', function (Blueprint $table) {
            $table->integer('uploaded_file_id')->after('station_id')->unsigned()->nullable()->default(null);

            $table->foreign('uploaded_file_id')->references('id')->on('uploaded_files')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uploaded_file_logs', function (Blueprint $table) {
            $table->dropForeign('uploaded_file_logs_uploaded_file_id_foreign');

            $table->dropColumn('uploaded_file_id');
        });
    }
}
