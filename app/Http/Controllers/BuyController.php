<?php

namespace App\Http\Controllers;

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
            return redirect(static::teleport($good->link));
        }
    }

    private static function teleport($link)
    {
        $parts = parse_url($link);
        parse_str($parts['query'], $query);

        $url = collect([
            $parts['scheme'], '://',
            $parts['host'],
            Str::replace('/g/', '/tptv2/', $parts['path']),
        ])->join('');

        $query['user_agent'] = request()->header('user-agent');
        $query['referer'] = request()->header('referer');
        $query['ip_addr'] = request()->ip();
        // $query['ip_addr'] = '188.163.15.253';
        $response = Http::get($url, $query);
        if ($response->ok()) {
            return json_decode($response->body())[0];
        }
        Log::warning("Teleport: " . $response->body());
        Log::warning("Teleport link: " . $link);
        return $link;
    }
}
