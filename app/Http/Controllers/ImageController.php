<?php

namespace App\Http\Controllers;

use App\Models\Good;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;

class ImageController extends Controller
{
    public static function downloadImage($src, $dst)
    {
        $ua = (string)Str::of(Str::of(File::get(storage_path('../user-agents.txt')))->explode("\n")->random())->trim();
        try {
            $img = Http::withOptions([
                'verify' => false,
                'curl'   => [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1'],
                'http_errors' => false,
            ])->withHeaders([
                'User-Agent' => $ua,
            ])->retry(3)->get($src);

            if ($img->failed()) abort(410);
            File::makeDirectory(File::dirname($dst), 0777, true, true);
            File::put($dst, $img->body());
            //remove all metadara
            $_command = 'convert ' . $dst . '  -delete 1--1 -strip ' . $dst; //удаляем фреймы и метатеги
            exec($_command);
            if (!File::exists($dst)) {
                abort(500);
            }
        } catch (\Exception $ex) {
            // abort(404);
            // $imgageCacheFile = $door->imgBlankPhoto;
        }
    }

    public function good(Good $good, $index)
    {
        $pics = Str::of($good->pictures)->explode(',');
        if ($pics->count() <= $index) abort(404);
        $picUrl = trim((string)$pics[$index]);

        $urlMd5 = md5($picUrl);
        $urlMd5 = [(string)Str::substr($urlMd5, 0, 2), (string)Str::substr($urlMd5, 2, 2), $urlMd5];
        $imageCache = config('feed.imgCache');
        $imageCacheFile = $imageCache . '/' . implode('/', $urlMd5) . '.jpg';
        if (!File::exists($imageCacheFile)) {
            self::downloadImage($picUrl, $imageCacheFile);
            if (!File::exists($imageCacheFile)) abort(404);
        }

        $imgFilename = public_path('imgs/' . app()->domain() . '/' . $good->sku . '/' . $index . '.jpg');
        if (!File::exists($imgFilename)) {
            File::makeDirectory(File::dirname($imgFilename), 0777, true, true);
            File::copy($imageCacheFile, $imgFilename);
        }

        return response()->file($imgFilename);
    }
}
