<?php

namespace App\Http\Middleware;

use App\Contracts\Enums\UserType;
use App\Contracts\Traits\HasResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    use HasResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()){
            $user = Auth::user();
            if($user->type == UserType::ADMIN->value || $user->type == UserType::SUPER_ADMIN->value)
            {
                return $next($request);
            }else{
                return $this->errorResponse('You\'re not admin');
            }
        }
        abort(400);
    }
}
