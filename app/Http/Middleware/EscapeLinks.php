<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EscapeLinks
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('app.escape_links')->contains($request->getRequestUri())) {
            return redirect()->route('home');
        }
        return $next($request);
    }
}
