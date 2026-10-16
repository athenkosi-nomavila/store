<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Audio' => 'Headphones, earbuds and speakers for music on the move or at home.',
            'Wearables' => 'Smartwatches and fitness bands that keep pace with your day.',
            'Home & Living' => 'Lighting, textiles and decor that make a space feel like yours.',
            'Kitchen' => 'Tools and tableware for slow mornings and good dinners.',
            'Workspace' => 'Desk essentials for a tidy, comfortable place to work.',
        ];

        foreach ($categories as $name => $description) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description],
            );
        }
    }
}
