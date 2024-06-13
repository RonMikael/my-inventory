<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StocksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stocks')->insert([
            ['product_id' => 1, 'stock_room' => 'BUILDING 2', 'location' => 'HANGOVER', 'quantity' => 0],
            ['product_id' => 2, 'stock_room' => 'BUILDING 2', 'location' => 'RACK 9', 'quantity' => 8],
            ['product_id' => 2, 'stock_room' => 'BUILDING 2', 'location' => 'RACK 1', 'quantity' => 1],
            ['product_id' => 2, 'stock_room' => 'DISPLAY', 'location' => 'RACK 1', 'quantity' => 0],
            ['product_id' => 2, 'stock_room' => 'DISPLAY', 'location' => 'RACK 2', 'quantity' => 0],
            ['product_id' => 3, 'stock_room' => 'BUILDING 2', 'location' => 'RACK 1', 'quantity' => 1],
            ['product_id' => 4, 'stock_room' => 'DISPLAY', 'location' => 'RACK 1', 'quantity' => 4],
            ['product_id' => 5, 'stock_room' => 'DISPLAY', 'location' => 'RACK 2', 'quantity' => 1],
            ['product_id' => 5, 'stock_room' => 'DISPLAY', 'location' => 'RACK 3', 'quantity' => 2],
            // Add more stocks if needed
        ]);
    }
}
