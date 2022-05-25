<?php

namespace App;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Feed
{
    private static function baseParams(): array
    {
        return  [
            'token' => config('feed.api_token'),
            'host' => app()->domain(),
            'ln' => config('feed.lang'),
            'geo' => config('feed.geo'),
            'c' => config('feed.goods_count'),
        ];
    }

    public static function getFeed($query)
    {
        $params = self::baseParams();
        $params['q'] = $query;
        try {
            $response = Http::feed()->get('/', $params);
        } catch (\Exception $e) {
            return null;
        }
        if (!$response->successful()) return null;
        return $response->body();
    }
}
