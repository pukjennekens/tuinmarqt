<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'external_id',
        'type',
        'parent_id',
        'woocommerce_id',
    ];

    /**
     * Get the parent category
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Get the children categories
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    /**
     * Get the WooCommerce request array for the product category
     * 
     * @return array
     */
    public function toWooCommerceArray(): array
    {
        $data = [
            'name' => $this->name,
        ];

        if($this->parent && $this->parent->woocommerce_id)
            $data['parent'] = $this->parent->woocommerce_id;

        return $data;
    }
}
