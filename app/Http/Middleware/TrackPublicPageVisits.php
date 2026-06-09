<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPublicPageVisits
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        $sessionId = $request->hasSession() ? (string) $request->session()->getId() : '';
        $ip = (string) $request->ip();
        $today = now();

        PageVisit::query()->create([
            'route_name' => $request->route()?->getName(),
            'path' => '/' . ltrim($request->path(), '/'),
            'url' => $request->fullUrl(),
            'visitor_hash' => sha1($today->toDateString() . '|' . ($sessionId !== '' ? $sessionId : $ip)),
            'ip_hash' => sha1($ip),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'referer' => substr((string) $request->headers->get('referer', ''), 0, 1000),
            'visited_on' => $today->toDateString(),
            'visited_at' => $today,
        ]);

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->ajax() || $request->expectsJson()) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        $routeName = (string) ($request->route()?->getName() ?? '');

        if (
            str_starts_with($routeName, 'admin.')
            || str_starts_with($routeName, 'login')
            || str_starts_with($routeName, 'register')
            || str_starts_with($routeName, 'password.')
            || str_starts_with($routeName, 'verification.')
            || str_starts_with($routeName, 'profile.')
            || $routeName === 'dashboard'
            || $routeName === 'orders.download'
            || $routeName === 'storage.local'
        ) {
            return false;
        }

        $contentType = strtolower((string) $response->headers->get('content-type', ''));

        if (! str_contains($contentType, 'text/html')) {
            return false;
        }

        return true;
    }
}
