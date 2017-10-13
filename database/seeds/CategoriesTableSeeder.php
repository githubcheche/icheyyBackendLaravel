<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('categories')->insert(array(

            0 =>
                array(
                    'id' => 1,
                    'name' => '技术',
                    'description' => '技术',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            1 =>
                array(
                    'id' => 2,
                    'name' => '编成',
                    'description' => '编成',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            2 =>
                array(
                    'id' => 3,
                    'name' => '生活',
                    'description' => '生活',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            3 =>
                array(
                    'id' => 4,
                    'name' => '娱乐',
                    'description' => '娱乐',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),

        ));
    }
}
