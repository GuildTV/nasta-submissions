<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_results', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('category_id')->unique();

            $table->integer('winner_id')->unsigned()->nullable()->unique();
            $table->string('winner_comment');
            $table->integer('commended_id')->unsigned()->nullable()->unique();
            $table->string('commended_comment');

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('winner_id')->references('id')->on('entries')->onDelete('restrict');
            $table->foreign('commended_id')->references('id')->on('entries')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_results');
    }
}
