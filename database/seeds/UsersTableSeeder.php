<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'cheyy',
                    'avatar' => 'no',
                    'email' => 'cheyy@sina.com',
                    'password' => '123456',
                    'confirm_code' => '123456789',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'cheyy1',
                    'email' => 'cheyy1@sina.com',
                    'avatar' => 'no',
                    'password' => '123456',
                    'confirm_code' => '123456789',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            2 =>
                array (
                    'id' => 3,
                    'name' => 'cheyy3',
                    'email' => 'cheyy3@sina.com',
                    'avatar' => 'no',
                    'password' => '123456',
                    'confirm_code' => '123456789',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            3 =>
                array (
                    'id' => 4,
                    'name' => 'cheyy4',
                    'email' => 'cheyy4@sina.com',
                    'password' => '123456',
                    'avatar' => 'no',
                    'confirm_code' => '123456789',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
            4 =>
                array (
                    'id' => 5,
                    'name' => 'cheyy5',
                    'email' => 'cheyy5@sina.com',
                    'password' => '123456',
                    'avatar' => 'no',
                    'confirm_code' => '123456789',
                    'created_at' => '2017-04-26 16:00:00',
                    'updated_at' => '2017-05-09 05:26:15',
                ),
        ));
    }
}
