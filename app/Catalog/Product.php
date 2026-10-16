<?php

namespace App\Catalog;

use Illuminate\Contracts\Routing\UrlRoutable;

/**
 * A product from the sample catalogue (config/catalog.php).
 */
final class Product implements UrlRoutable
{
    /** Stock at or below this level is shown as "Only N left". */
    public const LOW_STOCK_THRESHOLD = 5;

    public readonly string $stock_status;

    public readonly string $stock_label;

    public readonly string $formatted_price;

    public readonly ?string $image_url;

    public function __construct(
        public readonly string $slug,
        public readonly string $sku,
        public readonly string $name,
        public readonly Category $category,
        public readonly float $price,
        public readonly int $quantity,
        public readonly ?string $image = null,
        public readonly string $description = '',
        public readonly bool $is_active = true,
        public readonly int $position = 0,
    ) {
        $this->stock_status = match (true) {
            $quantity <= 0 => 'out',
            $quantity <= self::LOW_STOCK_THRESHOLD => 'low',
            default => 'in',
        };
        $this->stock_label = match ($this->stock_status) {
            'out' => 'Out of stock',
            'low' => "Only {$quantity} left",
            default => 'In stock',
        };
        $this->formatted_price = 'R '.number_format($price, 2, '.', ',');
        // Only file names from the catalogue are used, never user input.
        $this->image_url = $image ? asset('images/products/'.basename($image)) : null;
    }

    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    public function matches(string $term): bool
    {
        return str_contains(mb_strtolower($this->name), mb_strtolower($term))
            || str_contains(mb_strtolower($this->description), mb_strtolower($term));
    }

    public function getRouteKey(): string
    {
        return $this->slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return app(Catalog::class)->product($value);
    }

    public function resolveChildRouteBinding($childType, $value, $field): ?self
    {
        return null;
    }
}
