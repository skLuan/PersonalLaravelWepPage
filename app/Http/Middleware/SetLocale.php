<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);

        // Set the application locale
        App::setLocale($locale);

        // Store locale in session for persistence
        Session::put('locale', $locale);

        // route('…') calls without a locale (e.g. binshops' own views and models)
        // get the current one filled in.
        URL::defaults(['locale' => $locale]);

        // Controllers receive route parameters by position. Drop {locale} so
        // package controllers (binshops) get their own arguments, not "es".
        $request->route()?->forgetParameter('locale');

        return $next($request);
    }

    /**
     * Determine the locale for the current request.
     */
    protected function determineLocale(Request $request): string
    {
        $availableLocales = config('app.available_locales', ['en']);

        // 1. Check for locale prefix in URL (e.g., /es/home)
        $locale = $request->segment(1);
        if (in_array($locale, $availableLocales)) {
            return $locale;
        }

        // 2. Check session
        $locale = Session::get('locale');
        if ($locale && in_array($locale, $availableLocales)) {
            return $locale;
        }

        // 3. Check browser language preferences
        $locale = $this->getBrowserLocale($request, $availableLocales);
        if ($locale) {
            return $locale;
        }

        // 4. Fallback to default
        return config('app.locale', 'en');
    }

    /**
     * Get the best matching locale from browser Accept-Language header.
     */
    protected function getBrowserLocale(Request $request, array $availableLocales): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = explode(',', $acceptLanguage);

        foreach ($languages as $language) {
            $locale = trim(explode(';', $language)[0]);

            // Check exact match
            if (in_array($locale, $availableLocales)) {
                return $locale;
            }

            // Check language prefix (e.g., 'es' for 'es-ES')
            $prefix = explode('-', $locale)[0];
            if (in_array($prefix, $availableLocales)) {
                return $prefix;
            }
        }

        return null;
    }
}
