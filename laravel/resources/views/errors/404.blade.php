<!doctype html>
<html lang="sk">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>Stránka sa nenašla – SSTN</title>
  <meta name="theme-color" content="#00295c">
  <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('assets/fonts/archivo-latin-standard-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="{{ asset('assets/fonts/instrument-sans-latin-standard-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ \App\Support\Assets::url('assets/css/style.css') }}">
  <script>document.documentElement.classList.add('js')</script>
</head>
<body class="error-page">

<header class="site-header">
  <div class="site-header__bar">
    <a class="brand" href="{{ route('home') }}" aria-label="SSTN – Slovenská spoločnosť pre technickú normalizáciu, úvodná stránka">
      <img class="brand__mark" src="{{ asset('assets/img/sstn-logo-mark.webp') }}" width="480" height="159" alt="">
      <span class="brand__name" aria-hidden="true">Slovenská spoločnosť pre<br>technickú normalizáciu</span>
    </a>
    <a class="btn btn--ghost btn--sm legal__back" href="{{ route('home') }}">Späť na úvodnú stránku</a>
  </div>
</header>

{{-- Same millimetre grid, glows and dimension line as the home page hero --}}
<main id="obsah" class="hero error">
  <div class="container">
    <p class="chip hero__eyebrow"><span class="chip__dot" aria-hidden="true"></span>Chyba 404</p>

    <p class="error__code" aria-hidden="true">
      <span class="hero__accent">404
        <span class="dim">
          <span class="dim__ext dim__ext--l"></span>
          <span class="dim__line"></span>
          <span class="dim__ext dim__ext--r"></span>
          <span class="dim__label">MIMO TOLERANCIE</span>
        </span>
      </span>
    </p>

    <h1 class="error__title">Táto stránka <span class="accent">nie je v&nbsp;norme</span></h1>
    <p class="hero__lead">Stránku na tejto adrese sme nenašli. Mohla sa presunúť pri obnove webu alebo je v&nbsp;odkaze preklep.</p>

    <div class="hero__actions">
      <a class="btn btn--navy" href="{{ route('home') }}">Na úvodnú stránku</a>
      <a class="btn btn--ghost" href="{{ route('home') }}#podujatia">Podujatia</a>
      <a class="btn btn--ghost" href="{{ route('home') }}#kontakt">Kontakt</a>
    </div>
  </div>
</main>

<footer class="site-footer site-footer--compact">
  <div class="container site-footer__bottom">
    <p>© {{ now()->year }} Slovenská spoločnosť pre technickú normalizáciu</p>
    <p class="site-footer__legal"><a href="{{ route('cookies.policy') }}">Zásady cookies</a></p>
  </div>
</footer>
</body>
</html>
