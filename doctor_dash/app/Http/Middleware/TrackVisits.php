<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $path = $request->path();

            // لا نسجل زيارات لوحة التحكم أو مسار فحص الدخول أو الملفات الثابتة (صور، فيديو، CSS, JS, ...)
            $isAdminPath = $request->is('admin/*');
            $isAuthStatus = $request->is('auth/status');
            $isStaticAsset = (bool) preg_match('/\.(js|css|png|jpe?g|gif|webp|svg|ico|mp4|webm|ogg|mp3|wav|pdf|txt|json)$/i', $path);

            if (!$isAdminPath && !$isAuthStatus && !$isStaticAsset) {
                Visit::create([
                    'user_id' => optional($request->user())->id,
                    'ip_address' => $request->ip(),
                    'path' => $request->path(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                ]);
            }
        } catch (\Throwable $e) {
            // لا نكسر الطلب لو حصل خطأ في تسجيل الزيارة
        }

        return $response;
    }
}
