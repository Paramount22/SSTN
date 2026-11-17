@php
    use App\Support\Cookies;

    $optional = array_filter($categories, fn ($category) => ! $category['required']);
    $updated = \Illuminate\Support\Carbon::parse(config('cookies.policy_updated'))->translatedFormat('j. F Y');
@endphp
<!doctype html>
<html lang="sk">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Zásady cookies – SSTN</title>
  <meta name="description" content="Aké súbory cookies používa web Slovenskej spoločnosti pre technickú normalizáciu, na čo slúžia a ako zmeniť svoj súhlas.">
  <link rel="canonical" href="{{ route('cookies.policy') }}">
  <meta name="theme-color" content="#00295c">
  <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('assets/fonts/archivo-latin-standard-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="{{ asset('assets/fonts/instrument-sans-latin-standard-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ \App\Support\Assets::url('assets/css/style.css') }}">
</head>
<body>

<header class="site-header">
  <div class="site-header__bar">
    <a class="brand" href="{{ route('home') }}" aria-label="SSTN – Slovenská spoločnosť pre technickú normalizáciu, úvodná stránka">
      <img class="brand__mark" src="{{ asset('assets/img/sstn-logo-mark.webp') }}" width="480" height="159" alt="">
      <span class="brand__name" aria-hidden="true">Slovenská spoločnosť pre<br>technickú normalizáciu</span>
    </a>
    <a class="btn btn--ghost btn--sm legal__back" href="{{ route('home') }}">Späť na úvodnú stránku</a>
  </div>
</header>

<main id="obsah" class="section legal">
  <div class="container legal__body">
    <p class="eyebrow">Ochrana súkromia</p>
    <h1 class="section-title">Zásady <span class="accent">cookies</span></h1>
    <p class="legal__lead">Cookies sú malé textové súbory, ktoré stránka ukladá vo vašom prehliadači. Niektoré sú nevyhnutné, aby stránka fungovala. Ostatné používame iba s vaším súhlasom.</p>

    <h2>Aké cookies používame</h2>
    @foreach ($categories as $category)
      <section class="legal__category">
        <h3>{{ $category['label'] }} cookies @if ($category['required'])<span class="cc-always">Vždy aktívne</span>@endif</h3>
        <p>{{ $category['description'] }}</p>
        @include('site.partials.cookie-table', ['cookies' => $category['cookies']])
      </section>
    @endforeach
    @unless ($optional)
      <p>Momentálne nepoužívame analytické ani marketingové cookies a nenačítavame obsah tretích strán, ktorý by cookies ukladal. Ak ich v budúcnosti zavedieme, použijeme ich až po vašom súhlase a tieto zásady aktualizujeme.</p>
    @endunless

    <h2>Právny základ</h2>
    <p>Nevyhnutné cookies ukladáme bez súhlasu, pretože bez nich nemožno poskytnúť službu, ktorú ste si vyžiadali (§ 109 zákona č. 452/2021 Z. z. o elektronických komunikáciách). Ostatné cookies ukladáme len na základe vášho súhlasu (čl. 6 ods. 1 písm. a) nariadenia GDPR). Súhlas je dobrovoľný a jeho odmietnutie nemá vplyv na prístup k obsahu stránky.</p>

    <h2>Ako zmeniť alebo odvolať súhlas</h2>
    <p>Svoje rozhodnutie môžete kedykoľvek zmeniť v nastaveniach cookies. Odvolanie súhlasu je rovnako jednoduché ako jeho udelenie. Po odvolaní cookies danej kategórie vymažeme. Vaše rozhodnutie si pamätáme {{ Cookies::duration((int) config('cookies.lifetime_days'), 'days') }}, potom sa vás opýtame znova.</p>
    <p><button type="button" class="btn btn--navy btn--sm" data-cc-open>Nastavenia cookies</button></p>
    <p>Cookies môžete vymazať alebo zablokovať aj v nastaveniach svojho prehliadača. Ak zablokujete nevyhnutné cookies, niektoré časti stránky nemusia fungovať.</p>

    <h2>Záznam o súhlase</h2>
    <p>Aby sme vedeli preukázať, že sme súhlas získali (čl. 7 ods. 1 GDPR), zaznamenávame vaše rozhodnutie: náhodný identifikátor uložený vo vašej cookie, zvolené kategórie, verziu týchto zásad a čas. Neukladáme IP adresu ani údaje o prehliadači. Záznamy uchovávame {{ Cookies::duration((int) config('cookies.log_retention_days'), 'days') }} a potom ich automaticky vymažeme.</p>

    @if ($email)
      <h2>Kontakt</h2>
      <p>S otázkami k cookies a ochrane osobných údajov sa na nás obráťte na adrese <a href="mailto:{{ $email }}">{{ $email }}</a>.</p>
    @endif

    <p class="legal__updated">Posledná aktualizácia: {{ $updated }}</p>
  </div>
</main>

<footer class="site-footer site-footer--compact">
  <div class="container site-footer__bottom">
    <p>© {{ now()->year }} Slovenská spoločnosť pre technickú normalizáciu</p>
    <p class="site-footer__legal">
      <a href="{{ route('cookies.policy') }}">Zásady cookies</a>
      <button type="button" class="cookie-settings" data-cc-open>
        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M3 6h8m4 0h2M3 14h2m4 0h8"/><circle cx="13" cy="6" r="2"/><circle cx="7" cy="14" r="2"/></svg>
        Nastavenia cookies
      </button>
    </p>
  </div>
</footer>

@include('site.partials.cookie-consent')
</body>
</html>
