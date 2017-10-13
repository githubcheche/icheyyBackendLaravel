<?php

use Illuminate\Database\Seeder;

class ArticleTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('article_tag')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'article_id' => 1,
                    'tag_id' => 1,
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            1 =>
                array(
                    'id' => 2,
                    'article_id' => 1,
                    'tag_id' => 2,
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            2 =>
                array(
                    'id' => 3,
                    'article_id' => 2,
                    'tag_id' => 3,
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            3 =>
                array(
                    'id' => 4,
                    'article_id' => 2,
                    'tag_id' => 4,
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
        ));
    }
}
