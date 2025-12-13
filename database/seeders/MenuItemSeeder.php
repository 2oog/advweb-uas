<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Foods
        MenuItem::create([
            'name' => 'Nasi Goreng Special',
            'price' => 25000,
            'image_asset' => 'rice'  // Icon
        ]);
        MenuItem::create([
            'name' => 'Mie Goreng Seafood',
            'price' => 28000,
            'image_asset' => 'https://via.placeholder.com/200?text=Mie+Goreng'  // URL
        ]);
        MenuItem::create([
            'name' => 'Ayam Bakar',
            'price' => 30000,
            'image_asset' => 'drumstick-bite'  // Icon
        ]);
        MenuItem::create([
            'name' => 'Sate Ayam (10x)',
            'price' => 35000,
            'image_asset' => 'https://via.placeholder.com/200?text=Sate+Ayam'  // URL
        ]);

        // 2. Drinks
        MenuItem::create([
            'name' => 'Es Teh Manis',
            'price' => 5000,
            'image_asset' => 'glass-water'  // Icon
        ]);
        MenuItem::create([
            'name' => 'Es Jeruk',
            'price' => 8000,
            'image_asset' => 'https://via.placeholder.com/200?text=Es+Jeruk'  // URL
        ]);
        MenuItem::create([
            'name' => 'Kopi Hitam',
            'price' => 10000,
            'image_asset' => 'coffee'  // Icon
        ]);
        MenuItem::create([
            'name' => 'Mineral Water 600ml',
            'price' => 4000,
            'image_asset' => 'bottle-water'  // Icon
        ]);

        // 3. Condiments / Extras
        MenuItem::create([
            'name' => 'Sambal Terasi',
            'price' => 3000,
            'image_asset' => 'pepper-hot'  // Icon
        ]);
        MenuItem::create([
            'name' => 'Nasi Putih',
            'price' => 5000,
            'image_asset' => 'bowl-rice'  // Icon
        ]);
        MenuItem::create([
            'name' => 'Kerupuk Putih',
            'price' => 2000,
            'image_asset' => 'cookie'  // Icon
        ]);
        MenuItem::create([
            'name' => 'Telur Dadar',
            'price' => 5000,
            'image_asset' => 'egg'  // Icon
        ]);
    }
}
