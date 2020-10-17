<?php
/*
* File: GeoIPServiceProvider.php
* Category: ServiceProvider
* Author: M.Goldenbaum
* Created: 18.10.20 00:42
* Updated: -
*
* Description:
*  -
*/

namespace Webklex\LaravelGeoIP\Providers;

use Illuminate\Support\ServiceProvider;
use Webklex\GeoIP\GeoIP;

/**
 * Class GeoIPServiceProvider
 *
 * @package Webklex\IMAP\Providers
 */
class GeoIPServiceProvider extends ServiceProvider {

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {
        $this->publishes([
            __DIR__ . '/../config/geoip.php' => config_path('geoip.php'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register() {
        $this->setVendorConfig();

        $this->app->singleton(GeoIP::class, function($app) {
            return new GeoIP($app['config']["geoip"]["endpoint"]);
        });
    }

    /**
     * Merge the vendor settings with the local config
     *
     * The default account identifier will be used as default for any missing account parameters.
     * If however the default account is missing a parameter the package default account parameter will be used.
     * This can be disabled by setting imap.default in your config file to 'false'
     */
    private function setVendorConfig() {
        $config_key = 'geoip';
        $path = __DIR__ . '/../config/' . $config_key . '.php';

        $vendor_config = require $path;
        $config = $this->app['config']->get($config_key);

        $this->app['config']->set($config_key, $this->array_merge_recursive_distinct($vendor_config, $config));
    }

    /**
     * Marge arrays recursively and distinct
     *
     * Merges any number of arrays / parameters recursively, replacing
     * entries with string keys with values from latter arrays.
     * If the entry or the next value to be assigned is an array, then it
     * automatically treats both arguments as an array.
     * Numeric entries are appended, not replaced, but only if they are
     * unique
     *
     * @param array $array1 Initial array to merge.
     * @param array ...     Variable list of arrays to recursively merge.
     *
     * @return array|mixed
     *
     * @link   http://www.php.net/manual/en/function.array-merge-recursive.php#96201
     * @author Mark Roduner <mark.roduner@gmail.com>
     */
    private function array_merge_recursive_distinct() {

        $arrays = func_get_args();
        $base = array_shift($arrays);

        if (!is_array($base)) $base = empty($base) ? array() : array($base);

        foreach ($arrays as $append) {

            if (!is_array($append)) $append = array($append);

            foreach ($append as $key => $value) {

                if (!array_key_exists($key, $base) and !is_numeric($key)) {
                    $base[$key] = $append[$key];
                    continue;
                }

                if (is_array($value) or is_array($base[$key])) {
                    $base[$key] = $this->array_merge_recursive_distinct($base[$key], $append[$key]);
                } else if (is_numeric($key)) {
                    if (!in_array($value, $base)) $base[] = $value;
                } else {
                    $base[$key] = $value;
                }

            }

        }

        return $base;
    }

}