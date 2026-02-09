<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $supported = config('app.supported_locales', ['en']);

        $validated = $request->validate([
            'locale' => ['required', 'in:' . implode(',', $supported)],
        ]);

        $locale = $validated['locale'];

        $request->session()->put('app_locale', $locale);

        if ($request->user() && $request->user()->locale !== $locale) {
            $request->user()->forceFill(['locale' => $locale])->save();
        }

        return back();
    }
}
