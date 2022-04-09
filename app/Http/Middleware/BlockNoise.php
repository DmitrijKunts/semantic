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
            abort(410);
            // if (app()->domain() == 'atletikclub.ru') {
            //     dd(request()->headers->get('referer'));
            // } else {
            //     abort(410);
            // }
        }
        return $next($request);
    }
}
