<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class StrToLower
{
    /**
     * 邮箱不区分大小写 把邮箱全部修改成小写
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('email')) $request->email = strtolower($request->email);
        return $next($request);
    }
}
