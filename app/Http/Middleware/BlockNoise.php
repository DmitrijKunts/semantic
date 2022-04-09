<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockNoise
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $inputs = collect($request->all())->diffKeys(['page' => 1, 'cat_page' => 1, '_token' => 1]);
        if ($inputs->count()) {
            $domains = [
                "ilikewater.ru",
                "ufacomfort.ru",
                "kamin-sbyt.ru",
                "dr-kadir.ru",
                "uralneftestroi.ru",
                "xn----8sbvgdgjm0bcl7gh.xn--p1ai",
                "temako-suchi.ru",
                "atletikclub.ru",
            ];
            // dd(app()->domain());

            if (!isBot() && in_array(app()->domain(), $domains)) {
                return redirect('https://ad.admitad.com/g/9c4ca2202b15d564433592c5d6d73b/');//redirect to pleer
            } else {
                abort(410);
            }
        }
        return $next($request);
    }
}
