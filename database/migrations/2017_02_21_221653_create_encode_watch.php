<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncodeWatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encode_watch', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('uploaded_file_id')->unsigned()->unique();
            $table->integer('job_id')->unsigned()->unique();

            $table->timestamps();

            $table->foreign('uploaded_file_id')->references('id')->on('uploaded_files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encode_watch');
    }
}
