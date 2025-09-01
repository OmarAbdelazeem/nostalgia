<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some categories to assign products to
        $electronicsCategory = Category::where('name', 'Electronics')->first();
        $vintageCategory = Category::where('name', 'Vintage Items')->first();
        
        // If categories don't exist, create them
        if (!$electronicsCategory) {
            $electronicsCategory = Category::create([
                'name' => 'Electronics',
                'description' => 'Electronic devices and gadgets'
            ]);
        }
        
        if (!$vintageCategory) {
            $vintageCategory = Category::create([
                'name' => 'Vintage Items',
                'description' => 'Vintage and retro items'
            ]);
        }

        // Create sample products
        $products = [
            [
                'name' => 'Vintage Polaroid Camera',
                'description' => 'Beautiful vintage Polaroid camera from the 1970s. Perfect working condition with original leather case.',
                'product_number' => 'VC-001',
                'price' => 299.99,
                'discount' => 15.00,
                'manufacturing_material' => 'Plastic and Metal',
                'manufacturing_country' => 'USA',
                'stock_quantity' => 3,
                'is_available' => true,
                'category_id' => $vintageCategory->id,
            ],
            [
                'name' => 'Retro Typewriter',
                'description' => 'Classic mechanical typewriter from the 1960s. Excellent condition with all keys working perfectly.',
                'product_number' => 'RT-002',
                'price' => 450.00,
                'discount' => 0.00,
                'manufacturing_material' => 'Metal and Plastic',
                'manufacturing_country' => 'Germany',
                'stock_quantity' => 1,
                'is_available' => true,
                'category_id' => $vintageCategory->id,
            ],
            [
                'name' => 'Vintage Vinyl Record Player',
                'description' => 'Authentic 1950s vinyl record player with built-in speakers. Fully restored and functional.',
                'product_number' => 'VRP-003',
                'price' => 599.99,
                'discount' => 20.00,
                'manufacturing_material' => 'Wood and Metal',
                'manufacturing_country' => 'USA',
                'stock_quantity' => 2,
                'is_available' => true,
                'category_id' => $vintageCategory->id,
            ],
            [
                'name' => 'Classic Film Camera',
                'description' => 'Professional 35mm film camera from the 1980s. Includes multiple lenses and carrying case.',
                'product_number' => 'CFC-004',
                'price' => 350.00,
                'discount' => 10.00,
                'manufacturing_material' => 'Metal and Glass',
                'manufacturing_country' => 'Japan',
                'stock_quantity' => 4,
                'is_available' => true,
                'category_id' => $vintageCategory->id,
            ],
            [
                'name' => 'Antique Pocket Watch',
                'description' => 'Beautiful gold-plated pocket watch from the early 1900s. Still keeps perfect time.',
                'product_number' => 'APW-005',
                'price' => 799.99,
                'discount' => 0.00,
                'manufacturing_material' => 'Gold-plated Brass',
                'manufacturing_country' => 'Switzerland',
                'stock_quantity' => 1,
                'is_available' => true,
                'category_id' => $vintageCategory->id,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
} 