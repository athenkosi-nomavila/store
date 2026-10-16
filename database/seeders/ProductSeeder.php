<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        // [category slug, name, price, quantity, image, description, active]
        $products = [
            ['audio', 'Aria Wireless Headphones', 2499.00, 24, 'headphones', "Over-ear headphones with active noise cancelling and 40 hours of battery life.\nSoft memory-foam cushions keep long listening sessions comfortable, and the fold-flat design slips into the included case."],
            ['audio', 'Pulse Bluetooth Speaker', 1299.00, 4, 'speaker', "A compact speaker with room-filling sound and a splash-resistant finish.\nPair two for stereo, or take one outside for 12 hours of playback."],
            ['audio', 'Echo True Wireless Earbuds', 1799.00, 0, 'earbuds', "Lightweight earbuds with a pocket-sized charging case.\nTouch controls, clear calls and a secure fit for the gym."],
            ['wearables', 'Orbit Smartwatch', 3299.00, 15, 'watch', "Track workouts, sleep and heart rate with a bright always-on display.\nWater resistant to 50 metres, with up to seven days of battery."],
            ['wearables', 'Stride Fitness Band', 899.00, 32, 'band', "A slim band that counts steps, monitors sleep and nudges you to move.\nThe soft silicone strap is comfortable enough to wear all night."],
            ['home-living', 'Halo Table Lamp', 749.00, 9, 'lamp', "A warm, dimmable lamp with a linen-textured shade and oak stem.\nGreat on a bedside table or a reading nook."],
            ['home-living', 'Woven Linen Throw', 599.00, 3, 'blanket', "A breathable throw in washed linen with contrast stripes.\nLayer it on the sofa in winter or the bed in summer."],
            ['home-living', 'Terracotta Vase Trio', 449.00, 18, 'vase', "Three hand-finished ceramic vases in complementary shapes and glazes.\nBeautiful empty, better with a few stems."],
            ['kitchen', 'Brew Pour-Over Coffee Set', 649.00, 12, 'coffee', "A glass carafe and ceramic dripper for a clean, bright cup.\nMakes up to four cups; dishwasher safe."],
            ['kitchen', 'Stoneware Mug Pair', 299.00, 40, 'mug', "Two generous 350 ml mugs in emerald and amber glaze.\nMicrowave and dishwasher safe."],
            ['kitchen', "Chef's Knife 20 cm", 1099.00, 0, 'knife', "A forged stainless-steel chef's knife with a walnut handle.\nBalanced for everyday chopping, slicing and dicing."],
            ['workspace', 'Ergo Aluminium Laptop Stand', 899.00, 21, 'stand', "Lift your screen to eye level and free up desk space.\nFolds flat and fits laptops from 11 to 16 inches."],
            ['workspace', 'Quiet Mechanical Keyboard', 1899.00, 5, 'keyboard', "A compact keyboard with quiet tactile switches and USB-C.\nConnects to three devices over Bluetooth."],
            // Inactive: hidden from the storefront, kept to show the is_active flag works.
            ['audio', 'Retro Cassette Player', 999.00, 7, null, 'Discontinued model kept for records.', false],
        ];

        Storage::disk('public')->makeDirectory('products');

        foreach ($products as $i => $row) {
            [$slug, $name, $price, $quantity, $image, $description] = $row;
            $active = $row[6] ?? true;

            $imagePath = null;
            if ($image) {
                $imagePath = "products/{$image}.jpg";
                Storage::disk('public')->put($imagePath, file_get_contents(__DIR__."/images/{$image}.jpg"));
            }

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $categories[$slug],
                    'name' => $name,
                    'sku' => sprintf('STR-%04d', $i + 1),
                    'description' => $description,
                    'price' => $price,
                    'quantity' => $quantity,
                    'is_active' => $active,
                    'image_path' => $imagePath,
                ],
            );

            // Stagger creation dates so "Latest arrivals" lists them in this order.
            $product->forceFill(['created_at' => now()->subDays($i)])->saveQuietly();
        }
    }
}
