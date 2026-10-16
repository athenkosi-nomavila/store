<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = Str::title($this->faker->unique()->words(3, true));

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => strtoupper(Str::random(8)),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 50, 5000),
            'quantity' => $this->faker->numberBetween(6, 50),
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
