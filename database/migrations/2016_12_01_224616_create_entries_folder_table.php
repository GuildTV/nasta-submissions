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
            $table->integer('entry_id')->unsigned()->unique();

            $table->string('folder_id');

            $table->timestamps();

            $table->foreign('entry_id')->references('id')->on('entries')->onDelete('cascade');
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
