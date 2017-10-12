<?php

use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('articles')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'title' => '标题1',
                    'body' => '1现在，我们已经能获得一篇文章的所有评论，接着再定义一个通过评论获得所属文章的关联。这个关联是 hasMany 关联的反向关联，在子级模型中使用 belongsTo 方法定义它：',
                    'user_id' => 1,
                    'last_comment_time' => '2017-04-26 16:00:00',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),

            1 =>
                array (
                    'id' => 2,
                    'title' => '标题2',
                    'body' => '2现在，我们已经能获得一篇文章的所有评论，接着再定义一个通过评论获得所属文章的关联。这个关联是 hasMany 关联的反向关联，在子级模型中使用 belongsTo 方法定义它：',
                    'user_id' => 2,
                    'last_comment_time' => '2017-04-26 16:00:00',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),

            2 =>
                array (
                    'id' => 3,
                    'title' => '标题3',
                    'body' => '3现在，我们已经能获得一篇文章的所有评论，接着再定义一个通过评论获得所属文章的关联。这个关联是 hasMany 关联的反向关联，在子级模型中使用 belongsTo 方法定义它：',
                    'user_id' => 3,
                    'last_comment_time' => '2017-04-26 16:00:00',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),

            3 =>
                array (
                    'id' => 4,
                    'title' => '标题4',
                    'body' => '4现在，我们已经能获得一篇文章的所有评论，接着再定义一个通过评论获得所属文章的关联。这个关联是 hasMany 关联的反向关联，在子级模型中使用 belongsTo 方法定义它：',
                    'user_id' => 4,
                    'last_comment_time' => '2017-04-26 16:00:00',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),

        ));
    }
}
