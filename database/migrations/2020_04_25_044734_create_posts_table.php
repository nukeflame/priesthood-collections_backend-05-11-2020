<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('author');
            $table->longText('content');
            $table->string('title');
            $table->string('status')->unique();
            $table->string('comment_status');
            $table->string('slug')->unique();
            $table->bigInteger('post_parent');
            $table->string('guid');
            $table->string('menu_order');
            $table->string('post_type');
            $table->bigInteger('comment_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
