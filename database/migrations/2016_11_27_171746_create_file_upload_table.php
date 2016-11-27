<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_uploads', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('station_id')->unsigned();
            $table->string('category_id');
            $table->integer('constraint_id')->unsigned();
            $table->string('account_id');
            $table->string('scratch_folder_id')->unique()->nullable();
            $table->string('final_file_id')->unique()->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('station_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('constraint_id')->references('id')->on('file_constraints')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('google_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_uploads');
    }
}
