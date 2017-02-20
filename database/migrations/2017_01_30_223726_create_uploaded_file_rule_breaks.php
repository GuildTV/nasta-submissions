<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadedFileRuleBreaks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_file_rule_breaks', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('uploaded_file_id')->unsigned()->unique();

            $table->enum('result', [ 'unknown', 'warning', 'break', 'ok', 'accepted', 'rejected', 'pending' ])->default('unknown');

            $table->string('mimetype');
            $table->integer('length');

            $table->text('metadata');

            $table->text('warnings');
            $table->text('errors');

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
        Schema::dropIfExists('uploaded_file_rule_breaks');
    }
}
