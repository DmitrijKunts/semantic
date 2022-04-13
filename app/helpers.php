<?php

use App\Banner;

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
    function pleerRedirect()
    {
        $domains = [
            "dr-kadir.ru",
            "uralneftestroi.ru",
            "xn----8sbvgdgjm0bcl7gh.xn--p1ai",
            "temako-suchi.ru",
            "atletikclub.ru",
            "fotkay-nsk.ru",
            "bocchicontrol.ru",
            "m-de.ru",
        ];
        if (in_array(app()->domain(), $domains)) {
            if (!isBot()) {
                return redirect('https://ad.admitad.com/g/9c4ca2202b15d564433592c5d6d73b/?subid1=lost&subid=' . urlencode(app()->domain())); //redirect to pleer
            } else {
                abort(503);
            }
        } else {
            abort(410);
        }
    }
}
