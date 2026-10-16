<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductAvailabilityTest extends TestCase
{
    #[DataProvider('stockLevels')]
    public function test_stock_status_and_label(int $quantity, string $status, string $label): void
    {
        $product = new Product(['quantity' => $quantity]);

        $this->assertSame($status, $product->stock_status);
        $this->assertSame($label, $product->stock_label);
        $this->assertSame($quantity > 0, $product->isInStock());
    }

    public static function stockLevels(): array
    {
        return [
            'sold out' => [0, 'out', 'Out of stock'],
            'last one' => [1, 'low', 'Only 1 left'],
            'threshold' => [5, 'low', 'Only 5 left'],
            'plenty' => [6, 'in', 'In stock'],
        ];
    }

    public function test_price_is_formatted_in_rand(): void
    {
        $this->assertSame('R 1,299.00', (new Product(['price' => 1299]))->formatted_price);
    }
}
