<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a single courier box item
        $length = 30.00;
        $width = 25.00;
        $height = 15.00;
        
        Item::create([
            'name' => 'Medium Courier Box',
            'code' => 'CB-MD-001',
            'description' => 'Standard courier box ideal for books, clothing, and medium-sized items',
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'weight_limit' => 5.00,
            'volume' => $length * $width * $height, // Calculate volume manually
            'price' => 8.99,
            'is_active' => true,
            'material' => 'Corrugated Cardboard',
            'color' => 'Brown',
            'stock_quantity' => 50,
        ]);
    }
}
