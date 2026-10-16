<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /** Stock at or below this level is shown as "Only N left". */
    public const LOW_STOCK_THRESHOLD = 5;

    /** @var list<string> */
    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'description',
        'price', 'quantity', 'image', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Products shown on the storefront. */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** Newest first, with the id as a tie-breaker so the order is stable. */
    public function scopeNewest(Builder $query): void
    {
        $query->orderByDesc('created_at')->orderByDesc('id');
    }

    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    /** "in", "low" or "out". */
    protected function stockStatus(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->quantity <= 0 => 'out',
            $this->quantity <= self::LOW_STOCK_THRESHOLD => 'low',
            default => 'in',
        });
    }

    protected function stockLabel(): Attribute
    {
        return Attribute::get(fn () => match ($this->stock_status) {
            'out' => 'Out of stock',
            'low' => "Only {$this->quantity} left",
            default => 'In stock',
        });
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::get(fn () => 'R '.number_format((float) $this->price, 2, '.', ','));
    }

    protected function imageUrl(): Attribute
    {
        // basename() keeps a stored path from pointing outside the folder.
        return Attribute::get(fn () => $this->image ? asset('images/products/'.basename($this->image)) : null);
    }
}
