<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
     /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * The "type" of the primary key ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Cache key for the settings
     * 
     * @var string
     */
    protected static $cacheKey = 'global';

    /**
     * Get a setting
     * 
     * @param string $key
     * @return mixed Returns the value or null if the setting does not exists or is not configured
     */
    public static function get(string $key): mixed
    {
        if(Cache::has(self::$cacheKey . $key)) return Cache::get(self::$cacheKey . $key);

        $setting = self::find($key);

        if($setting) {
            Cache::put(self::$cacheKey . $key, $setting->value, 60 * 60 * 24);
            return $setting->value;
        }

        return null;
    }

    /**
     * Set a setting
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::put(self::$cacheKey . $key, $value, 60 * 60 * 24);
    }
}
