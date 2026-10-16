<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    /** Stock at or below this level is shown as "Only N left". */
    public const LOW_STOCK_THRESHOLD = 5;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'description',
        'price', 'quantity', 'is_active', 'image_path',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Match the term against name and description. The term only ever travels
     * as a bound parameter; LIKE wildcards typed by the user ("%" and "_") are
     * escaped with "!" so they are matched literally on MySQL and SQLite alike.
     */
    public function scopeSearch(Builder $query, string $term): void
    {
        $like = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term).'%';

        $query->where(function (Builder $q) use ($like) {
            $q->whereRaw("name LIKE ? ESCAPE '!'", [$like])
                ->orWhereRaw("description LIKE ? ESCAPE '!'", [$like]);
        });
    }

    public function scopeSortBy(Builder $query, ?string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price')->orderBy('id'),
            'name' => $query->orderBy('name')->orderBy('id'),
            default => $query->latest()->orderByDesc('id'),
        };
    }

    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    /** One of "in", "low" or "out". */
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
        return Attribute::get(fn () => $this->image_path ? asset('storage/'.$this->image_path) : null);
    }
}
