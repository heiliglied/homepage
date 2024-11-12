<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(
			[
				'user_id' => 'heiliglied',
				'password' => bcrypt('abcd1234'),
				'roll' => 1,
				'name' => '관리자',
				'email' => 'heiliglied@gmail.com',
			]
		);
    }
}
