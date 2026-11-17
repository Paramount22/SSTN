<?php

namespace App\Http\Controllers;

use App\Models\CookieConsent;
use App\Models\TextBlock;
use App\Support\Cookies;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CookieConsentController extends Controller
{
    /** The cookie policy page ("Zásady cookies"). */
    public function policy(): View
    {
        return view('site.cookies', [
            'categories' => Cookies::categories(),
            'email' => TextBlock::get('contact.email'),
        ]);
    }

    /** Records a visitor's choice as proof of consent; the choice itself lives in their cookie. */
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'consent_id' => ['required', 'uuid'],
            'version' => ['required', 'integer', Rule::in([(int) config('cookies.version')])],
            'categories' => ['present', 'array'],
            'categories.*' => ['string', 'distinct', Rule::in(Cookies::optional())],
        ]);

        CookieConsent::create($data);

        return response()->noContent();
    }
}
