<?php

namespace App\Http\Middleware\v1\user\general;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\Permissions\RoleEnum;
use Symfony\Component\HttpFoundation\Response;

class AllowSuperOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authUser = $request->user();

        // 1. It is super user do not allow access
        if ($authUser->role_id !== RoleEnum::super->value) {
            return response()->json([
                'message' => __('app_translation.unauthorized'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
        return $next($request);
    }
}
