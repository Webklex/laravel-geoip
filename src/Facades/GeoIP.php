<?php
/*
* File: GeoIP.php
* Category: Facade
* Author: M.Goldenbaum
* Created: 18.10.20 00:46
* Updated: -
*
* Description:
*  -
*/

namespace Webklex\LaravelGeoIP\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class GeoIP
 *
 * @package Webklex\LaravelGeoIP\Facades
 *
 * @method array current()
 * @method array get($ip)
 */
class GeoIP extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return \Webklex\GeoIP\GeoIP::class;
    }
}