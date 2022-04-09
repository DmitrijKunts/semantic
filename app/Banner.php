<?php

namespace App;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Banner
{
    private static $banner = null;

    public static function getBannerUrl($id)
    {
        if (!File::exists(storage_path('banners.txt'))) abort(500);
        $line = Str::of(File::get(storage_path('banners.txt')))->explode("\t");
        if ($line->count() < 2) abort(500);
        return $line->get(0);
    }

    public static function getBanner()
    {
        if (self::$banner) return self::$banner;
        if (!File::exists(storage_path('banners.txt'))) return null;
        $line = Str::of(File::get(storage_path('banners.txt')))->explode("\t");
        if ($line->count() < 2) return null;
        self::$banner = view('banner', [
            'href' => 0,
            'src' => $line->get(1),
        ]);
        return self::$banner;
    }
}
