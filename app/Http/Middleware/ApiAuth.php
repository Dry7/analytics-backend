<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class ApiAuth
{
    private const X_API_KEY = 'X-API-KEY';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        if ($request->header(self::X_API_KEY, null) !== config('scraper.api_key')) {
            return 'Invalid API key';
        }

        return $next($request);
    }
}
