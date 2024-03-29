<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @return array
    */
    protected $fillable = [
        'product_id',
        'external_id',
    ];

    /**
     * Get the product that owns the image
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * When the image gets deleted, delete the image from woocommerce
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            
        });
    }
}
