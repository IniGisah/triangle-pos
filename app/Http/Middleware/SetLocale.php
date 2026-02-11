<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = config('app.supported_locales', ['id', 'en']);
        $supportedLocales = array_map('strtolower', $supportedLocales);

        $sessionLocale = $request->session()->get('app_locale');
        $userLocale = optional($request->user())->locale;
        $browserLocale = $request->getPreferredLanguage($supportedLocales);

        $resolved = $sessionLocale;

        if ($userLocale && in_array($userLocale, $supportedLocales, true)) {
            $resolved = $userLocale;
        }

        if (!$resolved && $browserLocale) {
            $resolved = $browserLocale;
        }

        if (!$resolved) {
            $resolved = config('app.locale');
        }

        $request->session()->put('app_locale', $resolved);
        app()->setLocale($resolved);

        return $next($request);
    }
}
