<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUploadedFilesAddReplacementId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uploaded_files', function (Blueprint $table) {
            $table->integer('replacement_id')->after('video_metadata_id')->unsigned()->nullable()->default(null);

            $table->foreign('replacement_id')->references('id')->on('uploaded_files')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uploaded_files', function (Blueprint $table) {
            $table->dropForeign('uploaded_files_replacement_id_foreign');

            $table->dropColumn('replacement_id');
        });
    }
}
