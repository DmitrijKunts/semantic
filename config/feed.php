<?php

return [
    'api_token' => env('FEED_API_TOKEN', ''),
    'api_url' => env('FEED_API_URL', ''),
    'lang' => env('FEED_LANG', 'en'),
    'geo' => env('FEED_GEO', 'en'),
    'goods_count' => env('FEED_GOODS_COUNT', 100),
    'goods_order' => env('FEED_GOODS_ORDER', 'cat_good.rank'),
    'img_cache' => env('FEED_IMG_CACHE', '/var/www/image_cache'),
    'update_every_days' => 10,//обновление товаров в днях
];
