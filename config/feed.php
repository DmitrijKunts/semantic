<?php

return [
    'api_key' => env('FEED_API_KEY', ''),
    'api_url' => env('FEED_API_URL', ''),
    'geo' => env('FEED_GEO', 'ru'),
    'lang' => env('FEED_LANG', 'ru'),
    'minPrice' => env('FEED_MIN_PRICE', 10),
    'goodsCount' => env('FEED_GOODS_COUNT', 20),
    'imgCache' => env('FEED_IMG_CACHE', '/var/www/image_cache'),
];
