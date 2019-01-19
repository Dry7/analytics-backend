<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Response;

class ApiAuth
{
    public const X_API_KEY = 'X-API-KEY';

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
            return response('Invalid API key', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
