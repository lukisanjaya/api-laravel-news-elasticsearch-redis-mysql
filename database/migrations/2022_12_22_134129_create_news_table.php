<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('author_id')->unsigned();
            $table->integer('category_id')->nullable()->unsigned();
            $table->integer('subcategory_id')->nullable()->unsigned();
            $table->string('title');
            $table->string('slug');
            $table->text('content');
            $table->string('teaser');
            $table->string('image');
            $table->string('image_caption');
            $table->dateTime('published_at');
            $table->softDeletes();
            $table->timestamps();

            $table->index('slug');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('author_id');


            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('subcategory_id')->references('id')->on('subcategories');
            $table->foreign('author_id')->references('id')->on('users');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
