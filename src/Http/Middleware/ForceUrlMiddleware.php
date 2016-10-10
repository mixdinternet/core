<?php

namespace Mixdinternet\Core\Http\Middleware;

use Closure;

class ForceUrlMiddleware
{
    public function handle($request, Closure $next)
    {
        $_server = $request->server();

        $protocol = ($_server['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $url = $protocol . $_server['HTTP_HOST'];

        if (config('app.url') == $url) {
            return $next($request);
        }

        return redirect(config('app.url'), 301);
    }
}