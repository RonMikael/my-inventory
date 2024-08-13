<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FreebieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('freebies')->insert([
            ['name' => 'Free Air Check'],
            ['name' => 'Free Tire Rotation'],
            ['name' => 'Free Tire Balancing'],
            ['name' => 'Free Roadside Assistance'],
            ['name' => 'Free Alignment Check']
        ]);
    }
}
