<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoMetadataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_metadata', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('width');
            $table->integer('height');
            $table->integer('duration');

            $table->timestamps();
        });

        Schema::table('uploaded_files', function (Blueprint $table) {
            $table->integer('video_metadata_id')->unsigned()->after('public_url')->unique()->nullable()->default(null);

            $table->foreign('video_metadata_id')->references('id')->on('video_metadata')->onDelete('set null');
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
            $table->dropForeign('uploaded_files_video_metadata_id_foreign');
            
            $table->dropColumn('video_metadata_id');
        });

        Schema::dropIfExists('video_metadata');
    }
}
