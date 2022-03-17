<?php

return [
    'api_token' => env('FEED_API_TOKEN', ''),
    'api_url' => env('FEED_API_URL', ''),
    'lang' => env('FEED_LANG', 'ru'),
    'goods_count' => env('FEED_GOODS_COUNT', 20),
    'goods_order' => env('FEED_GOODS_ORDER', 'name'),
    'img_cache' => env('FEED_IMG_CACHE', '/var/www/image_cache'),
];
