<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GeoIP Database
    |--------------------------------------------------------------------------
    |
    | Path to the MaxMind GeoLite2-City database used for IP geolocation
    | (login activity locations). Download it from your MaxMind account or
    | a mirror and place it here. When missing, locations resolve to null.
    |
    */

    'database' => env('GEOIP_DATABASE_PATH', storage_path('app/geoip/GeoLite2-City.mmdb')),

    'download_url' => env('GEOIP_DOWNLOAD_URL', ''),

];
