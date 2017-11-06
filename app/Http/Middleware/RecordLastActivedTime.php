<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

/**
 * 更新用户在线时间中间件
 * Class RecordLastActivedTime
 * @package App\Http\Middleware
 */
class RecordLastActivedTime extends BaseMiddleware
{
    /**
     * Handle an incoming request. 记录用户的最后登陆时间，采用中间件拦截
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            return $next($request);
        }

        $user = $this->auth->authenticate($token);

        if (! $user) {
            return $next($request);
        }

        $user->recordLastActivedAt();

        return $next($request);
    }
}
