<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(3, true));

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => 'STR-'.fake()->unique()->numerify('####'),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 50, 5000),
            'quantity' => fake()->numberBetween(0, 50),
            'image' => null,
            'is_active' => true,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(['quantity' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
