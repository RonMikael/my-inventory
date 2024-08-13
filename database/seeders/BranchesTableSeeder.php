<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('branches')->insert([
            [
                'name' => 'Branch 1',
                'location' => '123 Main Street, Cityville',
            ],
            [
                'name' => 'Branch 2',
                'location' => '456 Elm Street, Townsville',
            ],
        ]);
    }
}
