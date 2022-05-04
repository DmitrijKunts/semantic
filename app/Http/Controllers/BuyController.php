<?php

namespace App\Http\Controllers;

use App\Banner;
use App\Models\Good;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BuyController extends Controller
{
    public function go(Good $good)
    {
        if (isBot()) {
            return back();
        } else {
            return redirect(static::teleport($good->link, 'buy'));
        }
    }

    public function banner($id)
    {
        if (isBot()) {
            return back();
        } else {
            return redirect(static::teleport(Banner::getBannerUrl($id, request()->input('query')), 'banner'));
        }
    }

    public static function teleport($link, $subid1 = '', $referer = '')
    {
        if (!Str::contains($link, 'admitad')) return $link;

        $parts = parse_url($link);
        parse_str($parts['query'], $query);

        $url = collect([
            $parts['scheme'], '://',
            $parts['host'],
            Str::replace('/g/', '/tptv2/', $parts['path']),
        ])->join('');

        $req = request();
        $query['user_agent'] = $req->header('user-agent');
        $query['referer'] = $referer != '' ?: $req->header('referer');
        $query['ip_addr'] = $req->ip();
        // $query['ip_addr'] = '188.163.15.253';
        $query['subid'] = app()->domain();
        if ($subid1) {
            $query['subid1'] = $subid1;
            $query['subid2'] = $subid1 . '_' . app()->domain();
        }
        $response = Http::get($url, $query);
        if ($response->ok()) {
            return json_decode($response->body())[0];
        }
        Log::warning("Teleport: " . $response->body());
        Log::warning("Teleport link: " . $link);
        return $link . '&subid=' . urlencode(app()->domain());
    }
}
