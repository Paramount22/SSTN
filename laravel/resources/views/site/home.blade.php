@php
    use Illuminate\Support\Str;

    $t = fn (string $key, string $default = '') => $text[$key] ?? $default;
    $lines = fn (string $key) => array_values(array_filter(array_map('trim', preg_split('/\R/', $t($key)))));
    $paragraphs = fn (?string $value) => array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $value))));

    $heroTitle = $lines('hero.title');
    $next = $upcoming->first();
    $second = $upcoming->skip(1)->first();
    $document = $links->get('document', collect())->first();
    $payment = $t('membership.payment');
    $variableSymbol = $t('membership.variable_symbol');
    $iban = preg_replace('/\s+/', '', $t('membership.iban'));
    $email = $t('contact.email');

    $target = fn ($link) => $link->is_external ? ' target="_blank" rel="noopener"' : '';
@endphp
<!doctype html>
<html lang="sk">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SSTN – Slovenská spoločnosť pre technickú normalizáciu</title>
  <meta name="description" content="Slovenská spoločnosť pre technickú normalizáciu (SSTN), člen ZSVTS. Odborné semináre, Stretnutia normalizátorov Slovenska, členstvo a kontakt.">
  <link rel="canonical" href="{{ url('/') }}">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="sk_SK">
  <meta property="og:title" content="SSTN – Slovenská spoločnosť pre technickú normalizáciu">
  <meta property="og:description" content="Odborné semináre, Stretnutia normalizátorov Slovenska, členstvo a kontakt.">
  <meta property="og:url" content="{{ url('/') }}">
  <meta name="theme-color" content="#00295c">
  <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/png">
  <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-touch-icon.png') }}">

  <link rel="preload" href="{{ asset('assets/fonts/archivo-latin-standard-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="{{ asset('assets/fonts/instrument-sans-latin-standard-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ \App\Support\Assets::url('assets/css/style.css') }}">
  <script>document.documentElement.classList.add('js')</script>

  <script type="application/ld+json">@json($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
</head>
<body>

<a class="skip-link" href="#obsah">Preskočiť na obsah</a>

<header class="site-header" data-header>
  <div class="site-header__bar">
    <a class="brand" href="#top" aria-label="SSTN – Slovenská spoločnosť pre technickú normalizáciu, na začiatok stránky">
      <img class="brand__mark" src="{{ asset('assets/img/sstn-logo-mark.webp') }}" width="480" height="159" alt="">
      <span class="brand__name" aria-hidden="true">Slovenská spoločnosť pre<br>technickú normalizáciu</span>
    </a>

    <nav class="nav" aria-label="Hlavná navigácia">
      <ul class="nav__list" data-nav>
        @foreach ($menu as $item)
          <li><a class="nav__link" href="{{ $item->url }}"{!! $target($item) !!}>{{ $item->label }}</a></li>
        @endforeach
        <li class="nav__indicator" aria-hidden="true" data-nav-indicator></li>
      </ul>
    </nav>

    <a class="btn btn--navy btn--sm site-header__cta" href="#clenstvo">Stať sa členom</a>

    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
      <span class="menu-toggle__bars" aria-hidden="true"></span>
      <span class="sr-only">Menu</span>
    </button>
  </div>

  <div class="mobile-menu" id="mobile-menu" hidden data-menu>
    <ul>
      @foreach ($menu as $item)
        <li><a href="{{ $item->url }}"{!! $target($item) !!}>{{ $item->label }}</a></li>
      @endforeach
    </ul>
    <a class="btn btn--navy" href="#clenstvo">Stať sa členom</a>
  </div>
</header>

<main id="obsah">

  {{-- ============ HERO ============ --}}
  <section class="hero" id="top">
    <div class="container hero__intro">
      <p class="chip hero__eyebrow"><span class="chip__dot" aria-hidden="true"></span>{{ $t('hero.eyebrow') }}</p>

      <h1 class="hero__title">
        <span class="hero__line">{{ $heroTitle[0] ?? '' }}</span>
        <span class="hero__accent">{{ $heroTitle[1] ?? '' }}
          <span class="dim" aria-hidden="true">
            <span class="dim__ext dim__ext--l"></span>
            <span class="dim__line"></span>
            <span class="dim__ext dim__ext--r"></span>
            <span class="dim__label">STN · EN · ISO</span>
          </span>
        </span>
      </h1>

      <p class="hero__lead">{{ $t('hero.lead') }}</p>

      <div class="hero__actions">
        <a class="btn btn--navy" href="#podujatia">Najbližšie podujatia <span class="btn__icon" aria-hidden="true"><svg viewBox="0 0 20 20"><path d="M10 4v12m0 0-5-5m5 5 5-5"/></svg></span></a>
        <a class="btn btn--ghost" href="#clenstvo">Stať sa členom</a>
      </div>
    </div>

    <div class="container hero__stage">
      <figure class="hero__media">
        {{-- Photo: Unsplash, https://unsplash.com/photos/person-measuring-technical-drawings-with-caliper-07mSSc4bdZ8 --}}
        <img src="{{ asset('assets/img/hero-caliper-1800.webp') }}"
             srcset="{{ asset('assets/img/hero-caliper-900.webp') }} 900w, {{ asset('assets/img/hero-caliper-1800.webp') }} 1800w"
             sizes="(max-width: 1240px) 100vw, 1200px"
             width="1800" height="1000"
             alt="Meranie posuvným meradlom nad technickými výkresmi"
             fetchpriority="high">
      </figure>

      @if ($next)
        <article class="float-card float-card--next">
          <div class="float-card__top">
            <span class="label">Najbližšie podujatie</span>
            <span class="countdown" data-countdown="{{ $next->starts_on->toDateString() }}">Pripravujeme</span>
          </div>
          <p class="float-card__title">{{ $next->title }}</p>
          <p class="float-card__meta">{{ collect([$next->date_long, $next->location])->filter()->implode(' · ') }}</p>
          @if ($file = $next->attachments->first())
            <a class="file file--block" href="{{ $file->url }}" download>
              <span class="file__icon" aria-hidden="true"><svg viewBox="0 0 20 20"><path d="M10 3v10m0 0-4-4m4 4 4-4M4 16h12"/></svg></span>
              <span class="file__name">{{ $file->label }}</span>
              <span class="file__meta">{{ $file->extension }} · {{ $file->size_label }}</span>
            </a>
          @endif
        </article>
      @endif

      @if ($second)
        <article class="float-card float-card--side">
          <span class="label">{{ $second->date_long }}</span>
          <p class="float-card__title float-card__title--sm">{{ $second->title }}</p>
          <p class="float-card__meta">{{ $second->type_label }}</p>
        </article>
      @endif
    </div>

    <div class="container">
      <div class="partners">
        <div class="partners__text">
          <a class="link-arrow" href="https://www.zsvts.sk/organizacie/slovenska-spolocnost-pre-technicku-normalizaciu-sstn/" target="_blank" rel="noopener">SSTN je členom ZSVTS</a>
          <p>Podujatia pripravujeme spolu s partnermi zo Slovenska aj Česka.</p>
        </div>
        <ul class="partners__list">
          @php
            // Partner logos in public/assets/img, matched by the link's domain: [file, width, height, caption?].
            // caption = the mark has no name in it, so the partner's description is set next to it.
            // Partners without a logo show their name and description.
            $partnerLogos = [
              'zsvts.sk' => ['assets/img/zsvts-logo.png', 325, 95],
              'normoff.gov.sk' => ['assets/img/partners/unms-sr.webp', 509, 138],
              'cstn.cz' => ['assets/img/partners/cstn.png', 71, 63, true],
              'minedu.sk' => ['assets/img/partners/msvvam-sr.svg', 1702, 536],
            ];
            $logoFor = function (string $url) use ($partnerLogos) {
              $host = (string) parse_url($url, PHP_URL_HOST);
              foreach ($partnerLogos as $domain => $logo) {
                if ($host === $domain || Str::endsWith($host, '.'.$domain)) {
                  return $logo;
                }
              }
              return null;
            };
          @endphp
          @foreach ($links->get('partner', collect()) as $partner)
            @if ($logo = $logoFor($partner->url))
              <li>
                <a href="{{ $partner->url }}" target="_blank" rel="noopener" title="{{ $partner->description ?: $partner->label }}" @class(['partner-logo--caption' => $logo[3] ?? false])>
                  @if ($logo[3] ?? false)
                    <img src="{{ asset($logo[0]) }}" width="{{ $logo[1] }}" height="{{ $logo[2] }}" alt="{{ $partner->label }}">
                    <span>{{ $partner->description ?: $partner->label }}</span>
                  @else
                    <img src="{{ asset($logo[0]) }}" width="{{ $logo[1] }}" height="{{ $logo[2] }}" alt="{{ $partner->description ?: $partner->label }}">
                  @endif
                </a>
              </li>
            @else
              <li class="wordmark">
                <a href="{{ $partner->url }}" target="_blank" rel="noopener"><strong>{{ $partner->label }}</strong>@if ($partner->description)<span>{{ $partner->description }}</span>@endif</a>
              </li>
            @endif
          @endforeach
        </ul>
      </div>
    </div>
  </section>

  {{-- ============ PODUJATIA ============ --}}
  <section class="section" id="podujatia" aria-labelledby="podujatia-title">
    <div class="container">
      <div class="section-head" data-reveal>
        <div>
          <p class="eyebrow">Podujatia</p>
          <h2 class="section-title" id="podujatia-title">Semináre a&nbsp;stretnutia <span class="accent">normalizátorov</span></h2>
        </div>
        <p class="section-head__text">{{ $t('events.intro') }}</p>
      </div>

      @if ($upcoming->isNotEmpty())
        <h3 class="group-label" data-reveal>Pripravujeme</h3>
        <div class="events-upcoming">
          @foreach ($upcoming as $event)
            <article class="event" data-reveal @if ($loop->index) style="--d: {{ min($loop->index, 3) * 120 }}ms" @endif>
              <div class="event__head">
                <p class="date-block" aria-hidden="true"><span class="date-block__d">{{ $event->starts_on->format('j') }}</span><span class="date-block__m">{{ $event->starts_on->translatedFormat('M') }}</span><span class="date-block__y">{{ $event->starts_on->year }}</span></p>
                <div class="event__titles">
                  <span class="countdown" data-countdown="{{ $event->starts_on->toDateString() }}">Pripravujeme</span>
                  <h4 class="event__title">{{ $event->title }}</h4>
                </div>
              </div>
              @if ($event->intro)
                <p class="event__lead">{{ $event->intro }}</p>
              @endif
              @foreach ($paragraphs($event->body) as $paragraph)
                <p class="event__lead">{{ $paragraph }}</p>
              @endforeach
              <dl class="titleblock">
                <div class="titleblock__cell"><dt>Dátum</dt><dd>{{ $event->date_long }}</dd></div>
                @if ($event->location)
                  <div class="titleblock__cell"><dt>Miesto</dt><dd>{{ $event->location }}</dd></div>
                @endif
                @if ($event->attachments->isNotEmpty())
                  <div class="titleblock__cell titleblock__cell--wide">
                    <dt>Dokumenty</dt>
                    <dd>
                      @foreach ($event->attachments as $file)
                        <a class="file" href="{{ $file->url }}" download>
                          <span class="file__icon" aria-hidden="true"><svg viewBox="0 0 20 20"><path d="M10 3v10m0 0-4-4m4 4 4-4M4 16h12"/></svg></span>
                          <span class="file__name">{{ $file->label }}</span>
                          <span class="file__meta">{{ $file->extension }} · {{ $file->size_label }}</span>
                        </a>
                      @endforeach
                    </dd>
                  </div>
                @endif
              </dl>
            </article>
          @endforeach
        </div>
      @endif

      <h3 class="group-label" data-reveal>Uskutočnené podujatia</h3>
      <div class="past">
        @foreach ($pastByYear as $year => $events)
          <section class="past__year" aria-label="{{ $year }}" data-reveal>
            <p class="past__year-label">{{ $year }}</p>
            <ul class="past__list">
              @foreach ($events as $event)
                <li class="past-item">
                  <p class="past-item__date">{{ $event->date_long }}</p>
                  <div class="past-item__body">
                    <h4 class="past-item__title">{{ $event->title }}</h4>
                    @if ($event->intro)
                      <p>{{ $event->intro }}</p>
                    @endif
                    @foreach ($paragraphs($event->body) as $paragraph)
                      <p>{{ $paragraph }}</p>
                    @endforeach
                    @if ($event->location)
                      <p><strong>Miesto konania:</strong> {{ $event->location }}</p>
                    @endif
                  </div>
                  @if ($event->attachments->isNotEmpty())
                    <div class="past-item__files">
                      @foreach ($event->attachments as $file)
                        <a class="file file--compact" href="{{ $file->url }}" download>
                          <span class="file__name">{{ $file->label }}</span><span class="file__meta">{{ $file->extension }} · {{ $file->size_label }}</span>
                        </a>
                      @endforeach
                    </div>
                  @endif
                </li>
              @endforeach
            </ul>
          </section>
        @endforeach

        <div class="past__more" data-reveal>
          <p class="past__year-label">Archív</p>
          <p>Staršie podujatia sú zatiaľ na pôvodnej stránke:
            <a class="link-arrow" href="https://sstn.sk/" target="_blank" rel="noopener">Podujatia 2016 – 2024</a>
            <a class="link-arrow" href="https://sstn.sk/akcie/m8" target="_blank" rel="noopener">Akcie SSTN a ZSVTS 2011 – 2018</a>
          </p>
        </div>
      </div>
    </div>
  </section>

  {{-- ============ AKTUALITY ============ --}}
  @if ($articles->isNotEmpty())
    <section class="section" id="aktuality" aria-labelledby="aktuality-title">
      <div class="container">
        <div class="section-head" data-reveal>
          <div>
            <p class="eyebrow">Aktuality</p>
            <h2 class="section-title" id="aktuality-title">Zo&nbsp;seminárov a&nbsp;<span class="accent">podujatí</span></h2>
          </div>
          <p class="section-head__text">Krátke správy a fotografie z podujatí, ktoré SSTN pripravila alebo na ktorých sa jej členovia zúčastnili.</p>
        </div>

        <div class="news">
          @foreach ($articles as $article)
            <article class="news-card" data-reveal @if ($loop->index) style="--d: {{ $loop->index * 120 }}ms" @endif>
              @if ($article->image_url)
                <figure class="news-card__media">
                  <img src="{{ $article->image_url }}" alt="{{ $article->image_alt }}" loading="lazy"
                       @if ($article->image_width) width="{{ $article->image_width }}" height="{{ $article->image_height }}" @endif>
                </figure>
              @endif
              <div class="news-card__body">
                <p class="news-card__date"><time datetime="{{ $article->published_on->toDateString() }}">{{ $article->published_on->translatedFormat('j. F Y') }}</time></p>
                <h3 class="news-card__title">{{ $article->title }}</h3>
                {{-- Unescaped on purpose: Article sanitizes body to an allowlist on every write (App\Support\RichText). --}}
                <div class="news-card__text">{!! $article->body !!}</div>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  {{-- ============ O NÁS ============ --}}
  <section class="section section--white" id="o-nas" aria-labelledby="o-nas-title">
    <div class="container about">
      <div class="about__aside" data-reveal>
        <p class="eyebrow">O nás</p>
        <h2 class="section-title" id="o-nas-title">Poslanie <span class="accent">SSTN</span></h2>
        <p class="about__intro">{{ $t('mission.intro') }}</p>
        <figure class="about__media">
          {{-- Photo: Unsplash, https://unsplash.com/photos/crowd-of-people-sitting-on-chairs-inside-room-F2KRf_QfCqw --}}
          <img src="{{ asset('assets/img/seminar-audience-1400.webp') }}"
               srcset="{{ asset('assets/img/seminar-audience-800.webp') }} 800w, {{ asset('assets/img/seminar-audience-1400.webp') }} 1400w"
               sizes="(max-width: 900px) 100vw, 480px"
               width="1400" height="1000" loading="lazy"
               alt="Účastníci odbornej konferencie v sále">
          <figcaption class="chip about__chip"><span class="chip__dot" aria-hidden="true"></span>Semináre, kurzy a výmena skúseností</figcaption>
        </figure>
      </div>

      <div class="about__main">
        <ul class="mission">
          @foreach ($lines('mission.items') as $item)
            <li data-reveal>{{ $item }}</li>
          @endforeach
        </ul>
        <a class="link-arrow about__more" href="https://sstn.sk/poslanie-sstn/vynatok-zo-stanov-sstn/m7" target="_blank" rel="noopener" data-reveal>Výňatok zo Stanov SSTN</a>
      </div>
    </div>
  </section>

  {{-- ============ ČLENSTVO ============ --}}
  <section class="section" id="clenstvo" aria-labelledby="clenstvo-title">
    <div class="container">
      <div class="section-head" data-reveal>
        <div>
          <p class="eyebrow">Členstvo</p>
          <h2 class="section-title" id="clenstvo-title">Pridajte sa <span class="accent">k&nbsp;normalizátorom</span></h2>
        </div>
        <p class="section-head__text">{{ $t('membership.intro') }}</p>
      </div>

      <p class="fees__caption" data-reveal>{{ $t('membership.fees_caption') }}</p>

      <ul class="fees">
        @foreach ($fees as $fee)
          <li @class(['fee', 'fee--featured' => $fee->is_featured]) data-reveal @if ($loop->index) style="--d: {{ $loop->index * 80 }}ms" @endif>
            <p class="fee__kind"><span class="label">Druh členstva</span>{{ $fee->kind }}</p>
            <p class="fee__price"><span class="fee__amount">{{ Str::before($fee->price_label, ' EUR') }}</span> EUR <span class="fee__sub">Členský príspevok</span></p>
            <div class="fee__votes">
              <span class="label">Počet hlasov a účastníkov na akciách</span>
              <span class="pips" role="img" aria-label="{{ $fee->votes }}">@for ($i = 1; $i <= max(5, $fee->votes); $i++)<i @class(['on' => $i <= $fee->votes])></i>@endfor<b>{{ $fee->votes }}</b></span>
            </div>
          </li>
        @endforeach
      </ul>

      <div class="join">
        <div class="join__apply" data-reveal>
          <p class="join__vat">{{ $t('membership.vat') }}</p>
          @if ($document)
            <a class="file file--block file--navy" href="{{ $document->url }}" download>
              <span class="file__icon" aria-hidden="true"><svg viewBox="0 0 20 20"><path d="M10 3v10m0 0-4-4m4 4 4-4M4 16h12"/></svg></span>
              <span class="file__name">{{ $document->label }}</span>
              @if ($document->description)
                <span class="file__meta">{{ $document->description }}</span>
              @endif
            </a>
          @endif
        </div>

        <div class="join__pay" data-reveal style="--d: 120ms">
          <p class="join__warn">
            @if (Str::contains($payment, '!!!'))
              <strong>{{ Str::before($payment, '!!!') }}!!!</strong>{{ Str::after($payment, '!!!') }}
            @else
              {{ $payment }}
            @endif
          </p>
          <dl class="pay">
            @if ($iban)
              <div class="pay__row pay__row--iban">
                <dt>IBAN</dt>
                <dd><span class="pay__iban" data-iban>{{ trim(chunk_split($iban, 4, ' ')) }}</span>
                  <button class="copy-btn" type="button" data-copy="{{ $iban }}">Kopírovať</button>
                </dd>
              </div>
            @endif
            @if ($variableSymbol)
              <div class="pay__row">
                <dt>{{ Str::contains($variableSymbol, ':') ? Str::beforeLast($variableSymbol, ':') : 'Variabilný symbol' }}</dt>
                <dd>{{ trim(Str::afterLast($variableSymbol, ':')) }}</dd>
              </div>
            @endif
          </dl>
          <p class="join__note">{{ $t('membership.note') }}</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ============ KONTAKT ============ --}}
  <section class="section section--tight" id="kontakt" aria-labelledby="kontakt-title">
    <div class="container">
      <div class="contact" data-reveal>
        <div class="contact__intro">
          <p class="eyebrow eyebrow--light">Kontakt</p>
          <h2 class="section-title section-title--light" id="kontakt-title">Ozvite sa <span class="accent accent--light">nám</span></h2>
          <p>{{ $t('contact.intro') }}</p>
          @if ($email)
            <a class="btn btn--white" href="mailto:{{ $email }}">Napísať e-mail</a>
          @endif
        </div>
        <div class="contact__cards">
          <div class="contact-card">
            <p class="label label--light">Poštová adresa</p>
            <address>{!! collect($lines('contact.address'))->map(fn ($line) => e($line))->implode('<br>') !!}</address>
          </div>
          <div class="contact-card">
            <p class="label label--light">Tajomník</p>
            <p class="contact-card__name">{{ $t('contact.secretary') }}</p>
            @if ($email)
              <p>e-mail: <a href="mailto:{{ $email }}">{{ $email }}</a></p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<footer class="site-footer">
  <div class="container site-footer__grid">
    <div class="site-footer__brand">
      <img class="site-footer__logo" src="{{ asset('assets/img/sstn-logo.webp') }}" width="900" height="414" alt="SSTN – Slovenská spoločnosť pre technickú normalizáciu" loading="lazy">
      <p>Slovenská spoločnosť pre technickú normalizáciu<br>Koceľova 15, 815 94 Bratislava</p>
    </div>

    <nav class="site-footer__col" aria-label="Na stránke">
      <p class="label">Na stránke</p>
      <ul>
        @foreach ($menu as $item)
          <li><a href="{{ $item->url }}"{!! $target($item) !!}>{{ $item->label }}</a></li>
        @endforeach
      </ul>
    </nav>

    @foreach (['footer' => 'Odkazy', 'video' => 'Videá'] as $group => $heading)
      @if ($links->has($group))
        <nav class="site-footer__col" aria-label="{{ $heading }}">
          <p class="label">{{ $heading }}</p>
          <ul>
            @foreach ($links->get($group) as $link)
              <li><a href="{{ $link->url }}" @if ($link->is_external) target="_blank" rel="noopener" @endif>{{ $link->label }}</a></li>
            @endforeach
          </ul>
        </nav>
      @endif
    @endforeach
  </div>

  <div class="container site-footer__bottom">
    <p>© {{ now()->year }} Slovenská spoločnosť pre technickú normalizáciu</p>
    <p class="site-footer__legal">
      <a href="{{ route('cookies.policy') }}">Zásady cookies</a>
      <button type="button" class="cookie-settings" data-cc-open>
        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M3 6h8m4 0h2M3 14h2m4 0h8"/><circle cx="13" cy="6" r="2"/><circle cx="7" cy="14" r="2"/></svg>
        Nastavenia cookies
      </button>
    </p>
    <p>Fotografie: Unsplash</p>
  </div>
</footer>

@include('site.partials.cookie-consent')

<script src="{{ \App\Support\Assets::url('assets/js/main.js') }}" defer></script>
</body>
</html>
