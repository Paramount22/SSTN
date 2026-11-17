@php
    use App\Support\Cookies;

    $categories = Cookies::categories();
    $optional = array_intersect_key($categories, array_flip(Cookies::optional()));
    $optionalLabels = collect($optional)->pluck('label')->map(fn ($label) => mb_strtolower($label))->join(', ', ' a ');
    $gaId = config('cookies.google_analytics_id');
@endphp

<div data-cookie-consent data-config='@json(Cookies::clientConfig())'>
  @if ($optional)
    {{-- First layer: rejecting is as easy and as visible as accepting. Shown by JS until a choice is made. --}}
    <section class="cc-banner" data-cc-banner hidden aria-label="Súhlas s cookies">
      <div>
        <p class="cc-banner__title">Súbory cookies</p>
        <p>Na fungovanie stránky používame nevyhnutné cookies. S vaším súhlasom použijeme aj {{ $optionalLabels }} cookies, ktoré nám pomáhajú stránku zlepšovať. Súhlas môžete kedykoľvek zmeniť alebo odvolať cez odkaz „Nastavenia cookies“ v pätičke. <a href="{{ route('cookies.policy') }}">Zásady cookies</a></p>
      </div>
      <div class="cc-banner__actions">
        <button type="button" class="btn btn--navy btn--sm" data-cc-reject>Odmietnuť všetky</button>
        <button type="button" class="btn btn--navy btn--sm" data-cc-accept>Prijať všetky</button>
        <button type="button" class="cc-link" data-cc-open>Nastavenia</button>
      </div>
    </section>
  @endif

  {{-- Second layer: choice per category, nothing pre-ticked. --}}
  <dialog class="cc-dialog" data-cc-dialog aria-labelledby="cc-title">
    <form method="dialog" class="cc-dialog__inner">
      <div class="cc-dialog__head">
        <h2 id="cc-title">Nastavenia cookies</h2>
        <button class="cc-close" value="close" aria-label="Zavrieť nastavenia cookies">
          <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M5 5l10 10M15 5 5 15"/></svg>
        </button>
      </div>

      <p class="cc-dialog__intro">
        @if ($optional)
          Vyberte, ktoré cookies smieme používať. Nevyhnutné cookies sú potrebné na fungovanie stránky. Ostatné použijeme len s vaším súhlasom, ktorý môžete kedykoľvek odvolať.
        @else
          Táto stránka používa len nevyhnutné cookies, ktoré sú potrebné na jej fungovanie. Nepoužívame analytické ani marketingové cookies, preto od vás nepotrebujeme súhlas.
        @endif
        <a href="{{ route('cookies.policy') }}">Zásady cookies</a>
      </p>

      @foreach ($categories as $key => $category)
        <section class="cc-cat">
          <div class="cc-cat__head">
            <h3 id="cc-cat-{{ $key }}">{{ $category['label'] }}</h3>
            @if ($category['required'])
              <span class="cc-always">Vždy aktívne</span>
            @else
              <label class="cc-switch">
                <input type="checkbox" role="switch" data-cc-category="{{ $key }}" aria-labelledby="cc-cat-{{ $key }}">
                <span class="cc-switch__track" aria-hidden="true"></span>
              </label>
            @endif
          </div>
          <p>{{ $category['description'] }}</p>
          <details>
            <summary>Zoznam cookies ({{ count($category['cookies']) }})</summary>
            @include('site.partials.cookie-table', ['cookies' => $category['cookies']])
          </details>
        </section>
      @endforeach

      <div class="cc-dialog__actions">
        @if ($optional)
          <button type="button" class="btn btn--ghost btn--sm" data-cc-save>Uložiť výber</button>
          <button type="button" class="btn btn--navy btn--sm" data-cc-reject>Odmietnuť všetky</button>
          <button type="button" class="btn btn--navy btn--sm" data-cc-accept>Prijať všetky</button>
        @else
          <button class="btn btn--navy btn--sm" value="close">Zavrieť</button>
        @endif
      </div>
    </form>
  </dialog>
</div>

{{-- Optional scripts stay inert (type="text/plain") until their category is accepted. --}}
@if ($gaId && isset($optional['analytics']))
  <script type="text/plain" data-cookie-category="analytics" data-src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($gaId) }}" async></script>
  <script type="text/plain" data-cookie-category="analytics">
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', @json($gaId));
  </script>
@endif

<script src="{{ \App\Support\Assets::url('assets/js/cookie-consent.js') }}" defer></script>
