<?php

use App\API\WooCommerce;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\EnsureWooCommerceWebhookSignatureValid;
use App\Jobs\ExportArticleGroups;
use App\Jobs\ExportArticles;
use App\Jobs\ImportArticleGroups;
use App\Jobs\ImportArticles;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::post('/webhook', [WebhookController::class, 'handle'])
    ->middleware(EnsureWooCommerceWebhookSignatureValid::class);

Route::get('/test', function() {
    Log::info('test', [
        Setting::get('woocommerce_website_url'),
        Setting::get('woocommerce_consumer_key'),
        Setting::get('woocommerce_consumer_secret'),
    ]);
    $products = WooCommerce::getClient()->get('products');
    throw new \Exception('test');
    return [
        Setting::get('woocommerce_website_url'),
        Setting::get('woocommerce_consumer_key'),
        Setting::get('woocommerce_consumer_secret'),
        $products,
    ];
});

Route::get('/import', function() {
    dispatch(new ImportArticleGroups());
    dispatch(new ImportArticles());

    return response()->json([
        'message' => 'Import started',
    ]);
});

Route::get('/export', function() {
    dispatch(new ExportArticleGroups());
    dispatch(new ExportArticles());;

    return response()->json([
        'message' => 'Export started',
    ]);
});