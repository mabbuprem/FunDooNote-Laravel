<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Faker;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1,2) as $index) {
            DB::table('users')->insert([
                'firstName' => $faker->name,
                'lastName' => $faker->name,
                'email' => $faker->unique()->email,
                'passWord'=>$faker->unique()->password
            ]);
        }
        
    }
}

