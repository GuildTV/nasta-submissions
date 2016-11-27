<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileUploadLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_upload_logs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('station_id')->unsigned();
            $table->string('category_id');
            $table->integer('constraint_id')->unsigned();

            $table->string('level');
            $table->string('message');

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('station_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('constraint_id')->references('id')->on('file_constraints')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_upload_logs');
    }
}
