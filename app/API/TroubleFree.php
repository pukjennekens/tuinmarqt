<?php

namespace App\API;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TroubleFree
{
    /**
     * @var string $apiUrl The URL of the TroubleFree API
     */
    private static $apiUrl = 'https://my.troublefree.nl/v3/api';

    /**
     * @var bool $isInitialized Whether the class has been initialized
     */
    private static $isInitialized = false;

    /**
     * @var array $headers The headers to use for the API requests
     */
    private static $headers = [];

    /**
     * Initialize the TroubleFree API
     * 
     * @throws \Exception If the TroubleFree API settings are not set
     * @return void
     */
    public static function initialize()
    {
        if (self::$isInitialized) return;

        self::$isInitialized = true;

        // Get the API credentials from the settings
        $troublefreeCompany   = Setting::get('troublefree_api_company');
        $troublefreeUsername  = Setting::get('troublefree_api_username');
        $troublefreePassword  = Setting::get('troublefree_api_password');
        $authenticationHeader = base64_encode($troublefreeUsername . ':' . $troublefreePassword);

        // Check if the settings are set, if not, stop the import and throw an error so we can see it in Pulse
        if (!$troublefreeCompany || !$troublefreeUsername || !$troublefreePassword) throw new \Exception('TroubleFree API settings are not set.');

        self::$headers = [
            'Authorization' => 'Basic ' . $authenticationHeader,
            'Accept'        => 'application/json',
            'Company'       => $troublefreeCompany,
        ];
    }

    /**
     * Make a request to the TroubleFree API
     * @param string $method The HTTP method to use
     * @param string $endpoint The endpoint to request
     * @param array $data The data to send with the request, if it's a GET request, this will be used as query parameters
     * @param bool $cache Whether to cache the response internally
     * @param int $ttl The time-to-live of the cache
     * @throws TroubleFreeException If the API request fails
     * @return mixed The response from the API
     */
    public static function request(
        string $method,
        string $endpoint,
        array $data = [],
        bool $cache = false,
        int $ttl = 3600
    )
    {
        $cacheKey = md5($method . '-' . $endpoint . '-' . json_encode($data));
        if($cache && Cache::has($cacheKey)) return Cache::get($cacheKey);

        $url = self::$apiUrl . $endpoint;
        
        Log::debug('Requesting data from TroubleFree API', ['url' => $url, 'method' => $method, 'data' => $data]);

        $response = Http::withHeaders(self::$headers)->{$method}($url, $data);

        Log::debug('Received data from TroubleFree API', ['status' => $response->status(), 'data' => $response->json()]);

        if($cache && $response->status() == 200) Cache::put($cacheKey, $response->json(), $ttl);

        if( ! in_array( $response->status(), [200, 201] ) ) throw new TroubleFreeException('TroubleFree API request failed', $response->status(), $response->json());

        return $response->json();
    }

    /**
     * Get articles from the article list
     * 
     * @param int $page The page to get
     * @param int $perPage The amount of articles to get per page
     * @param bool $webshopOnly Whether to only get articles that are available in the webshop
     * @param bool $cache Whether to cache the response
     * @param int $ttl The time-to-live of the cache
     * @return array The articles
     */
    public static function getArticles(int $page = 1, int $perPage = 100, bool $webshopOnly = true, bool $cache = true, int $ttl = 3600)
    {
        $query = [
            'page'         => $page,
            'per_page'     => $perPage,
            'webshop_only' => $webshopOnly ? 'true' : 'false',
        ];

        return self::request('get', '/articles', $query, $cache, $ttl);
    }

    /**
     * Get article main groups from the article list
     * 
     * @param int $page The page to get
     * @param int $perPage The amount of articles to get per page
     * @param bool $cache Whether to cache the response
     * @param int $ttl The time-to-live of the cache
     * @return array The article main groups
     */
    public static function getArticleMainGroups(int $page = 1, int $perPage = 100, bool $cache = true, int $ttl = 3600)
    {
        $query = [
            'page'     => $page,
            'per_page' => $perPage,
        ];

        return self::request('get', '/article_main_groups', $query, $cache, $ttl);
    }

    /**
     * Get article groups from the article list
     * 
     * @param int $page The page to get
     * @param int $perPage The amount of articles to get per page
     * @param bool $cache Whether to cache the response
     * @param int $ttl The time-to-live of the cache
     * @return array The article groups
     */
    public static function getArticleGroups(int $page = 1, int $perPage = 100, bool $cache = true, int $ttl = 3600)
    {
        $query = [
            'page'     => $page,
            'per_page' => $perPage,
        ];

        return self::request('get', '/article_groups', $query, $cache, $ttl);
    }

    /**
     * Get a file from the TroubleFree API
     * 
     * @param string $id The ID of the asset
     * @param bool $cache Whether to cache the response
     * @param int $ttl The time-to-live of the cache
     * @return \Illuminate\Http\Client\Response|false The asset or false if the asset could not be found
     */
    public static function getFile(string $id, bool $cache = true, int $ttl = 3600)
    {
        // $cacheKey = md5('troublefree_api_asset_' . $id);
        // if($cache && Cache::has($cacheKey)) return Cache::get($cacheKey);

        $asset = Http::withHeaders(self::$headers)->get(self::$apiUrl . '/files/' . $id . '/download', ['inline' => 'true']);

        if($asset->status() != 200) return false;

        // if($cache && $asset->status() == 200) Cache::put($cacheKey, $asset, $ttl);

        return $asset;
    }

    /**
     * Find a relation by its postal code and house number
     * 
     * @param string $postalCode The postal code of the relation
     * @param string $houseNumber The house number of the relation
     * @param bool $cache Whether to cache the response
     * @param int $ttl The time-to-live of the cache
     * @return array|false The relation or false if the relation could not be found
     */
    public static function findRelationByPostalCodeAndHouseNumber(string $postalCode, string $houseNumber, bool $cache = true, int $ttl = 3600)
    {
        $query = [
            'postal_code'  => $postalCode,
            'house_number' => $houseNumber,
            'page'         => 1,
            'per_page'     => 1,
        ];

        return self::request('get', '/relations', $query, $cache, $ttl);
    }

    /**
     * Create a relation
     * 
     * @param array $data The data to create the relation with
     * @return array|false The relation or false if the relation could not be created
     */
    public static function createRelation(array $data)
    {
        return self::request('post', '/relations', $data);
    }
}