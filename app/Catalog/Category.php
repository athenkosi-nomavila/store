<?php

namespace App\Catalog;

use Illuminate\Contracts\Routing\UrlRoutable;

/**
 * A product category from the sample catalogue (config/catalog.php).
 */
final class Category implements UrlRoutable
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly ?string $description = null,
        public int $products_count = 0,
    ) {}

    public function is(?self $other): bool
    {
        return $other !== null && $other->slug === $this->slug;
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
        return app(Catalog::class)->category($value);
    }

    public function resolveChildRouteBinding($childType, $value, $field): ?self
    {
        return null;
    }
}
