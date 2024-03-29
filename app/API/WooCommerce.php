<?php

namespace App\API;

use App\Models\Setting;
use Automattic\WooCommerce\Client;

class WooCommerce
{
    /**
     * @var bool $isInitialized Whether the class has been initialized
     */
    private static $isInitialized = false;

    /**
     * @var \Automattic\WooCommerce\Client $client The WooCommerce API client
     */
    private static $client;

    /**
     * Initialize the TroubleFree API
     * 
     * @throws \Exception If the WooCommerce API settings are not set
     * @return void
     */
    public static function initialize()
    {
        if (self::$isInitialized) return;

        self::$isInitialized = true;
    }

    /**
     * Get the WooCommerce API client
     *
     * @return \Automattic\WooCommerce\Client
     */
    public static function getClient()
    {
        $woocommerce_website_url     = Setting::get('woocommerce_website_url');
        $woocommerce_consumer_key    = Setting::get('woocommerce_consumer_key');
        $woocommerce_consumer_secret = Setting::get('woocommerce_consumer_secret');

        if(!$woocommerce_website_url || !$woocommerce_consumer_key || !$woocommerce_consumer_secret) throw new \Exception('WooCommerce API credentials are not set');

        $client = new Client($woocommerce_website_url, $woocommerce_consumer_key, $woocommerce_consumer_secret, [
            'version'    => 'wc/v3',
            'verify_ssl' => env('APP_ENV') === 'production',
            'timeout'    => 120,
        ]);

        self::$client = $client;

        return self::$client;
    }
}