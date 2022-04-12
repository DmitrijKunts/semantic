<?php

namespace App;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Banner
{
    private static $banner = [];

    public static function getBannerUrl($id, $query='')
    {
        if (!File::exists(storage_path('banners.txt'))) abort(500);
        $line = Str::of(File::get(storage_path('banners.txt')))->explode("\t");
        if ($line->count() < 2) abort(500);
        return Str::replace('{query}', urlencode($query), $line->get(0));
    }

    public static function getBanner($query = '')
    {
        //https://ad.admitad.com/g/9c4ca2202b15d564433592c5d6d73b/?ulp=https%3A%2F%2Fwww.pleer.ru%2Fsearch_%25D1%2587%25D0%25B5%25D1%2585%25D0%25BB%25D1%258B%2Bcanon.html
        if (isset(self::$banner[$query])) return self::$banner[$query];
        if (!File::exists(storage_path('banners.txt'))) return null;
        $line = Str::of(File::get(storage_path('banners.txt')))->explode("\t");
        if ($line->count() < 2) return null;
        self::$banner[$query] = view('banner', [
            'href' => 0,
            'src' => $line->get(1),
            'query' => $query,
        ]);
        return self::$banner[$query];
    }
}
