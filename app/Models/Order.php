<?php

namespace App\Models;

use App\API\TroubleFree;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'woocommerce_id',
        'data',
        'postal_code',
        'house_number',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Format the postal code on set
     * 
     * @param string $value
     * @return void
     */
    public function setPostalCodeAttribute($value)
    {
        $this->attributes['postal_code'] = strtoupper(str_replace(' ', '', $value));
    }

    /**
     * Format the house number on set
     * 
     * @param string $value
     * @return void
     */
    public function setHouseNumberAttribute($value)
    {
        $this->attributes['house_number'] = strtoupper(str_replace(' ', '', $value));
    }

    /**
     * On change or on create, check if is paid and set the is_exported flag
     * 
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            if(isset($order->data['status']) && ( $order->data['status'] === 'processing' || $order->data['status'] === 'completed' ) && ! $order->is_exported) {
                $response = TroubleFree::request('get', '/relations', ['email' => $order->data['billing']['email']]);
                $relation = $response['data'][0] ?? null;

                if(!$relation) {
                    $response = TroubleFree::request('post', '/relations', [
                        'firstname'            => $order->data['billing']['first_name'],
                        'lastname'             => $order->data['billing']['last_name'],
                        'company'              => $order->data['billing']['first_name'] . ' ' . $order->data['billing']['last_name'],
                        'phone'                => $order->data['billing']['phone'],
                        'email'                => $order->data['billing']['email'],
                        'group'                => 1,
                        'applyGroupProperties' => true,
                        'paymentConditions'    => [
                            [
                                'orderType'        => 1,
                                'paymentCondition' => 1,
                            ]
                        ],
                        'customerTFCode'       => $order->data['billing']['email'],
                    ]);

                    $relation = $response['data'];
                }

                $orderLines = [];

                foreach($order->data['line_items'] as $line) {
                    $orderLines[] = [
                        'article'  => Product::where('woocommerce_id', $line['product_id'])->first()->external_id,
                        'quantity' => strval( $line['quantity'] ),
                        'price'    => strval( floatval( $line['total'] ) / floatval( $line['quantity'] ) ),
                    ];
                }

                foreach($order->data['shipping_lines'] as $line) {
                    $orderLines[] = [
                        'article'  => Setting::get('troublefree_shipping_product_id'),
                        'quantity' => '1',
                        'price'    => strval( floatval( $line['total'] ) ),
                    ];
                }

                $response = TroubleFree::request('post', '/orders', [
                    'employee'        => 1,
                    'debtor'          => $relation['id'],
                    'deliveryMethod'  => 1,
                    'orderType'       => 1,
                    'reference'       => $order->data['number'],
                    'extraComment'    => $order->data['customer_note'] ?? 'Geen opmerkingen',
                    'deliveryAddress' => [
                        'attentionOf' => $order->data['billing']['first_name'] . ' ' . $order->data['billing']['last_name'],
                        'street'      => $order->data['shipping']['address_1'] ?? $order->data['billing']['address_1'],
                        'postcode'    => $order->data['shipping']['postcode'] ?? $order->data['billing']['postcode'],
                        'city'        => $order->data['shipping']['city'] ?? $order->data['billing']['city'],
                        'country'     => $order->data['shipping']['country'] ?? $order->data['billing']['country'],
                    ],
                    'lines'           => $orderLines,
                ]);

                if($response['status'] == 201) {
                    $order->is_exported = true;
                    $order->save();
                }
            }
        });
    }
}
