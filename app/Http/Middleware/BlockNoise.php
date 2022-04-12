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
        $inputs = collect($request->all())->diffKeys([
            'page' => 1,
            'cat_page' => 1,
            '_token' => 1,
            '_method' => 1,
            'query' => 1,
        ]);
        if ($inputs->count()) {
            return pleerRedirect();
        }
        return $next($request);
    }
}
