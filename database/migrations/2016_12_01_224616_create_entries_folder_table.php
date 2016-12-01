<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesFolderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries_folders', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('category_id');
            $table->integer('station_id')->unsigned();

            $table->string('folder_id');

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('station_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['category_id', 'station_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entries_folders');
    }
}
