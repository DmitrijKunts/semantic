<?php

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
