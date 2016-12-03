<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadedFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_files', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('station_id')->unsigned();
            $table->string('category_id')->nullable()->default(null);

            $table->string('account_id');
            $table->string('path');
            $table->string('name');

            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('dropbox_accounts')->onDelete('cascade');
            $table->foreign('station_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_files');
    }
}
