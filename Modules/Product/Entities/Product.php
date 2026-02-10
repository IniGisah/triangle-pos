<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Notifications\NotifyQuantityAlert;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{

    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    protected $with = ['media'];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('images')
            ->useFallbackUrl('/images/fallback_product_image.png');
    }

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('thumb')
            ->width(50)
            ->height(50);
    }

    public function setProductCostAttribute($value) {
        $this->attributes['product_cost'] = ($value * 100);
    }

    public function getProductCostAttribute($value) {
        return ($value / 100);
    }

    public function setProductPriceAttribute($value) {
        $this->attributes['product_price'] = ($value * 100);
    }

    public function getProductPriceAttribute($value) {
        return ($value / 100);
    }

    public function setWholesalePriceAttribute($value) {
        $this->attributes['wholesale_price'] = is_null($value) ? null : ($value * 100);
    }

    public function getWholesalePriceAttribute($value) {
        return is_null($value) ? null : ($value / 100);
    }

    /**
     * Get total quantity across both wholesale and retail units
     * Calculates: (wholesale_unit_stock × wholesale_quantity) + retail_unit_stock
     */
    public function getTotalQuantityAttribute() {
        $wholesaleStock = $this->wholesale_unit_stock ?? 0;
        $retailStock = $this->retail_unit_stock ?? 0;
        $wholesaleQuantity = $this->wholesale_quantity ?? 0;

        return ($wholesaleStock * $wholesaleQuantity) + $retailStock;
    }

    /**
     * Mutator for backward compatibility
     * Converts legacy product_quantity into boxes + pieces
     */
    public function setProductQuantityAttribute($value) {
        if (is_null($value) || $value < 0) {
            $value = 0;
        }

        $this->attributes['product_quantity'] = $value;

        // If wholesale is configured, split into boxes and pieces
        if (!empty($this->wholesale_quantity) && $this->wholesale_quantity > 0) {
            $boxes = floor($value / $this->wholesale_quantity);
            $pieces = $value % $this->wholesale_quantity;

            $this->attributes['wholesale_unit_stock'] = $boxes;
            $this->attributes['retail_unit_stock'] = $pieces;
        } else {
            // No wholesale unit, put everything in retail
            $this->attributes['wholesale_unit_stock'] = 0;
            $this->attributes['retail_unit_stock'] = $value;
        }
    }

    /**
     * Accessor for product_quantity - returns calculated total for backward compatibility
     */
    public function getProductQuantityAttribute($value) {
        // If dual inventory columns exist and have values, return calculated total
        if (isset($this->attributes['wholesale_unit_stock']) || isset($this->attributes['retail_unit_stock'])) {
            return $this->total_quantity;
        }

        // Fallback to stored value
        return $value ?? 0;
    }

    /**
     * Scope to filter products that are in stock
     */
    public function scopeInStock($query) {
        return $query->where(function($q) {
            $q->where('retail_unit_stock', '>', 0)
              ->orWhere('wholesale_unit_stock', '>', 0);
        });
    }

    /**
     * Check if product has low stock
     */
    public function hasLowStock() {
        $retailStock = $this->retail_unit_stock ?? 0;
        $wholesaleQuantity = $this->wholesale_quantity ?? 0;

        // Low stock if loose pieces are less than one wholesale unit
        return $wholesaleQuantity > 0 && $retailStock < $wholesaleQuantity;
    }
}
