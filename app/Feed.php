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
            'c' =>  config('feed.goods_count'),
        ];
    }

    public static function getFeed($query, $oid = null)
    {
        $params = self::baseParams();
        $params['q'] = $query;
        // if (config('app.debug')) {
        //     $params['lastserver'] = 1;
        //     dd(Http::feed()->withOptions([
        //         'debug' => true,
        //     ])->get('/', $params));
        // }
        $response = Http::feed()->get('/', $params);
        if (!$response->successful()) abort(500);
        return $response->body();
    }


    // public function checkTargetAvailable($target)
    // {
    //     $params = [
    //         '_gs_at' => $this->apiKey,
    //         'checktarget' => $target,
    //         'host' => request()->getHost(),
    //         'ln' => $this->lang,
    //         'lastserver' => 1, //тут важна скорость
    //     ];
    //     // if (config('app.debug')) {
    //     //     $params['lastserver'] = 1;
    //     //     dd(Http::retry(1, 1)->get($this->apiUrl, $params));
    //     // }
    //     $response = Http::retry(1, 500)->get($this->apiUrl, $params);

    //     if (!$response->successful()) return 1; //при ошибки возвращаем, что оффер существует, тут важна скорость
    //     return $response->body();
    // }

    // public function getImgInfoFromServer($url)
    // {
    //     $response = Http::retry(3, 30000)->get($this->apiUrl, [
    //         '_gs_at' => $this->apiKey,
    //         'hash2imginfo' => md5($url),

    //         'lastserver' => 1, //почему то не резолвится хост
    //     ]);
    //     if (!$response->successful()) return false;
    //     return $pic_info = (object)json_decode($response->body(), true);
    // }
}
