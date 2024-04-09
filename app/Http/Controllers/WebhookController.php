<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle the incoming webhook request
     * 
     * @param Request $request
     */
    public function handle(Request $request)
    {
        $topic = $request->header('x-wc-webhook-topic');

        Log::info('Webhook received', [
            'topic' => $topic,
            'data'  => $request->json()->all(),
        ]);
        
        if(in_array($topic, ['order.created', 'order.updated'])) {
            $this->handleOrderCreatedUpdated($request);
        }

        return response(null, 200);
    }

    /**
     * Handle the order.created webhook
     * 
     * @param Request $request
     */
    private function handleOrderCreatedUpdated(Request $request)
    {
        $houseNumberRegex = '/\b\d+(?:-\d+)?(?:\s*[A-Za-z]+)?(?:-\d+[A-Z]*)?\b/';
        
        // Get the last match of the house number regex
        $houseNumber = preg_match_all($houseNumberRegex, $request->json('billing.address_1'), $matches);
        $houseNumber = end($matches[0]);

        Order::updateOrCreate([
            'woocommerce_id' => $request->json('id')
        ],
        [
            'data'         => $request->json()->all(),
            'postal_code'  => $request->json('billing.postcode'),
            'house_number' => $houseNumber,
        ]);
    }
}
