<?php

use App\Banner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

if (!function_exists('constGen')) {
    function genConst($val, $noise = '')
    {
        return $val == 0 ? 0 : hexdec(substr(sha1($noise), 0, 15)) % $val;
    }
}

if (!function_exists('constSort')) {
    function constSort($items, $noise = '')
    {
        $items = collect($items);
        $size = $items->count();
        $items = $items->sortBy(function ($item) use ($size, $noise) {
            if (gettype($item) == 'array') {
                return genConst($size, (string)serialize($item) . $noise);
            } else {
                return genConst($size, (string)$item . $noise);
            }
        });
        return array_values($items->all());
    }
}

if (!function_exists('constOne')) {
    function constOne($items, $noise = '')
    {
        return collect(constSort($items, $noise))->first();
    }
}

if (!function_exists('scheme')) {
    function scheme()
    {
        $cf = request()->server('HTTP_CF_VISITOR');
        if ($cf) {
            $scheme = json_decode($cf, true);
            if (isset($scheme['scheme'])) {
                return $scheme['scheme'];
            } else {
                return 'http';
            }
        } else {
            return 'http';
        }
    }
}

if (!function_exists('isBot')) {
    function isBot()
    {
        $check_bot_is_bot = true;

        $check_bot_host = Cache::store('fileshared')->remember(
            'gethostbyaddr-' . request()->ip(),
            60 * 60 * 24,
            function () {
                return gethostbyaddr(request()->ip());
            }
        );
        // $check_bot_host = 'google.com';
        // $check_bot_host = 'google-proxy-74-125-208-83.google.com ';

        if (
            // !preg_match('~localhost|google|bing|msn|yahoo|yandex|accoona|ask|bond|bot|crawler|curl|eltaindexer|ia_archiver|jeeves|nigma|proximic|rambler|spider|turtle|w3c_validator|webalta|wget|facebook|ahrefs~i', $check_bot_host)
            !preg_match('~google|bing|msn|yahoo|yandex|accoona|ask|bond|bot|crawler|curl|eltaindexer|ia_archiver|jeeves|nigma|proximic|rambler|spider|turtle|w3c_validator|webalta|wget|facebook|ahrefs~i', $check_bot_host)
            // || preg_match('~google-proxy-~i', $check_bot_host) //google-proxy - через него смотрят "глазами" гугла
        ) {
            // if (!preg_match('~google|bing|msn|yahoo|yandex|accoona|ask|bond|bot|crawler|curl|eltaindexer|ia_archiver|jeeves|nigma|proximic|rambler|spider|turtle|w3c_validator|webalta|wget|facebook|ahrefs~i', $check_bot_host)) {
            $check_bot_is_bot = false;
        }

        // Log::info("IP:" . request()->ip() . ", host: $check_bot_host, bot: " . ($check_bot_is_bot ? 'yes' : 'no'));

        return $check_bot_is_bot;
    }
}

if (!function_exists('getBanner')) {
    function getBanner($query = '')
    {
        return Banner::getBanner($query);
    }
}

if (!function_exists('pleerRedirect')) {
    function pleerCheckUri($domain)
    {
        switch ($domain) {
            case 'dr-kadir.ru':
                $q = (string)Str::of(request()->getRequestUri())
                    ->match('~html\.php\?(.+?)(:?$|&)~isu')
                    ->replace('_', '%20');
                return $q ? "ulp=https%3A%2F%2Fwww.pleer.ru%2Fsearch_$q.html&" : null;
                break;
            case 'xn----8sbvgdgjm0bcl7gh.xn--p1ai':
                return "ulp=https%3A%2F%2Fwww.pleer.ru";
                break;
            case 'temako-suchi.ru':
                $q = (string)Str::of(request()->getRequestUri())
                    ->match('~/(.+?)\.shtm~isu')
                    ->replace('-', '%20');
                return $q ? "ulp=https%3A%2F%2Fwww.pleer.ru%2Fsearch_$q.html&" : null;
                break;
            case 'atletikclub.ru':
                $q = (string)Str::of(request()->getRequestUri())
                    ->match('~\?index_id=(.+?)(:?$|&)~isu')
                    ->replace('-', '%20');
                return $q ? "ulp=https%3A%2F%2Fwww.pleer.ru%2Fsearch_$q.html&" : null;
                break;
            case 'fotkay-nsk.ru':
                $q = (string)Str::of(request()->getRequestUri())
                    ->match('~container\.asp\?(.+?)(:?$|&)~isu')
                    ->replace('_', '%20');
                return $q ? "ulp=https%3A%2F%2Fwww.pleer.ru%2Fsearch_$q.html&" : null;
                break;
            case 'bocchicontrol.ru':
                $q = (string)Str::of(request()->getRequestUri())
                    ->match('~revision\.asp\?(.+?)(:?$|&)~isu')
                    ->replace('-', '%20');
                return $q ? "ulp=https%3A%2F%2Fwww.pleer.ru%2Fsearch_$q.html&" : null;
                break;
            case 'aquarium-best.ru':
                $q = (string)Str::of(request()->getRequestUri())
                    ->match('~container\.php\?(.+?)(:?$|&)~isu')
                    ->replace('-', '%20');
                return $q ? "ulp=https%3A%2F%2Fwww.pleer.ru%2Fsearch_$q.html&" : null;
                break;

            default:
                return null;
                break;
        }
    }

    function pleerRedirect()
    {
        $domains = [
            "semantic1.local",
            "dr-kadir.ru",
            "xn----8sbvgdgjm0bcl7gh.xn--p1ai",
            "temako-suchi.ru",
            "atletikclub.ru",
            "fotkay-nsk.ru",
            "bocchicontrol.ru",
            "m-de.ru",
            "aquarium-best.ru",
        ];
        if (in_array(app()->domain(), $domains)) {
            if (!isBot() && Str::of(request()->getRequestUri())->match('~\.jpg|\.gif|\.css|\.js~') == '') {
                $ulp = pleerCheckUri(app()->domain());
                // dd($ulp);
                if (!$ulp) abort(304);
                Log::channel('pleer')->info(request()->getRequestUri());
                return redirect("https://ad.admitad.com/g/9c4ca2202b15d564433592c5d6d73b/?{$ulp}subid1=lost_semantic&subid=" . urlencode(app()->domain())); //redirect to pleer
            } else {
                abort(304);
            }
        } else {
            abort(410);
        }
    }
}
