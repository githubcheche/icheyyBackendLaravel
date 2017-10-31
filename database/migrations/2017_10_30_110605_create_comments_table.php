<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * 评论表
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('评论者id');
            $table->text('body')->comment('评论');
            $table->unsignedInteger('commentable_id')->comment('');//多态关联主键
            $table->string('commentable_type')->comment('');//多态关联type
            $table->unsignedInteger('parent_id')->nullable()->comment('上一级评论id'); //上一级评论id
            $table->smallInteger('level')->default(1)->comment('评论的层级'); //评论的层级
            $table->string('is_hidden', 8)->default('F')->comment('是否不允许评论');
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
        Schema::dropIfExists('comments');
    }
}
