<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class JwtAuthenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {
            $this->authenticate($request);
        }catch (UnauthorizedHttpException $e) {
            return $this->respond($e->getMessage(), $e->getStatusCode());
        }

        return $next($request);
    }

    protected function respond($error, $status)
    {
        $result = [
            'status'    => 0,
            'message' => trans('jwt.' . $error),
        ];
        return response()->json($result, $status);
    }
}
