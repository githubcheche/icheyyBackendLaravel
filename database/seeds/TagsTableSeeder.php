<?php

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('tags')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'name' => 'webrtc',
                    'description' => 'webrtc技术',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            1 =>
                array(
                    'id' => 2,
                    'name' => 'web',
                    'description' => 'web技术',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            2 =>
                array(
                    'id' => 3,
                    'name' => '单片机',
                    'description' => '单片机技术',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            3 =>
                array(
                    'id' => 4,
                    'name' => 'android',
                    'description' => 'android技术',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
        ));
    }
}
