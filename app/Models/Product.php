<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'external_id',
        'woocommerce_id',
        'name',
        'data',
        'active',
    ];

    /**
     * The attributes that should be cast.
     * 
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the product images for the product
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the WooCommerce request array for the product
     * 
     * @return array
     */
    public function toWooCommerceArray(): array
    {
        $data = [
            'name'              => $this->name,
            'type'              => 'simple',
            'status'            => 'publish',
            'sku'               => $this->data['barCode'] ?? '',
            'short_description' => $this->data['description'] ?? '',
            'regular_price'     => $this->data['salesPrice']['inclVat'] ?? 0,
            'meta_data'         => [
                [
                    'key'   => 'external_id',
                    'value' => $this->external_id,
                ],
                [
                    'key'   => 'price_unit',
                    'value' => $this->data['unit'] ?? '',
                ],
            ],
            // 'images'            => $this->images->map(function($image) {
            //     return [
            //         'src' => route('image', ['id' => $image->external_id]) . '.jpeg',
            //     ];
            // })->toArray(),
        ];

        if($this->woocommerce_id) $data['id'] = $this->woocommerce_id;

        if(isset($this->data['dimensions'])) {
            $data['dimensions'] = [
                'length' => $this->data['dimensions']['length'] ?? 0,
                'width'  => $this->data['dimensions']['width'] ?? 0,
                'height' => $this->data['dimensions']['height'] ?? 0,
            ];
        }

        if(isset($this->data['piecesPerUnit'])) {
            $data['meta_data'][] = [
                'key'   => 'pieces_per_unit',
                'value' => $this->data['piecesPerUnit'],
            ];
        }

        foreach(config('constants.troublefree_custom_fields') as $field) {
            if(isset($this->data['customFields'][$field['name']])) {
                $data['meta_data'][] = [
                    'key'   => 'troublefree_' . $field['key'],
                    'value' => isset($this->data['customFields'][$field['name']]) ? explode(',', $this->data['customFields'][$field['name']]) : '',
                ];

                if($field['attribute'])
                    $data['attributes'][] = [
                        'name'    => $field['name'],
                        'options' => explode(',', $this->data['customFields'][$field['name']]),
                        'visible' => $field['visible'] ?? false,
                    ];
            }
        }

        if(isset($this->data['group']) && ProductCategory::where('external_id', $this->data['group'])->exists()) {
            $productCategory     = ProductCategory::where('external_id', $this->data['group'])->where('type', 'article_group')->firstOrFail();

            $data['categories'] = [
                [
                    'id' => $productCategory->woocommerce_id,
                ],
            ];

            if($productCategory->parent && $productCategory->parent->woocommerce_id) {
                $data['categories'][] = [
                    'id' => $productCategory->parent->woocommerce_id,
                ];
            }
        }

        Log::debug('Product to WooCommerce array', $data);

        return $data;
    }
}
