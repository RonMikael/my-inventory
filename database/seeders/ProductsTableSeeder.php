<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['size' => '4.00 X 8', 'reference_number' => '1112', 'price' => 1790.00, 'brand' => 'DEESTONE RIB', 'category_id' => 1],
            ['size' => '120/70-12', 'reference_number' => '1144/1092', 'price' => 1995.00, 'brand' => 'CST CM521 TL', 'category_id' => 1],
            ['size' => '130/70-12', 'reference_number' => '85/1228/12', 'price' => 2095.00, 'brand' => 'CST C6513', 'category_id' => 1],
            ['size' => '110/70x13', 'reference_number' => '1500', 'price' => 2090.00, 'brand' => 'CORSA PLATINUM CROSS S', 'category_id' => 1],
            ['size' => '110/70 X 13', 'reference_number' => '1329/1339', 'price' => 2200.00, 'brand' => 'CST C6525', 'category_id' => 1],
            // Add more products if needed
        ];

        foreach ($products as $productData) {
            $product = \App\Models\Product::create($productData);

            // Add multiple images for each product
            \App\Models\ProductImage::create(['product_id' => $product->id, 'image_path' => 'product_images/laptop1.jpg']);
            \App\Models\ProductImage::create(['product_id' => $product->id, 'image_path' => 'product_images/laptop2.jpg']);
        }
    }
}
