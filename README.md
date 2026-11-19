# SSTN
Slovenská spoločnosť pre technickú normalizáciu

## Štruktúra repozitára

- `site/` – statický návrh landing page (HTML/CSS/JS), publikovaný ako demo na GitHub Pages
- `laravel/` – Laravel aplikácia (Laravel 13, Blade + Alpine.js + Tailwind, SQLite, Breeze auth)
- `audit/`, `REDESIGN-PLAN.md` – audit pôvodného webu sstn.sk a plán redizajnu
- `tools/` – skripty použité pri audite a úprave loga

## Spustenie Laravel aplikácie lokálne

```bash
cd laravel
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Seeder naplní obsah z pôvodného webu (podujatia 2025–2026 s PDF, texty, členské príspevky, odkazy). Len v prostredí `local` (a v testoch) vytvorí aj vývojový administrátorský účet `admin@sstn.test` – jeho heslo je verejne v Gite, preto sa mimo lokálneho počítača nikdy nevytvára.

### Administrátor na serveri

Na serveri (`APP_ENV=production`) vytvorte administrátora príkazom, heslo zadáte do výzvy (neuloží sa nikde v kóde ani v histórii príkazov):

```bash
php artisan admin:create meno@sstn.sk --name="Meno Priezvisko"
```

Rovnaký príkaz nastaví existujúcemu účtu nové heslo a práva administrátora (napr. po zabudnutí hesla).

Administrácia, prihlásenie, zabudnuté heslo aj profil bežia pod tajnou cestou z `.env` (`ADMIN_PATH`, predvolene `admin`): napr. pri `ADMIN_PATH=sprava-7k2q9x` je prihlásenie na `/sprava-7k2q9x/login`, a adresy `/admin`, `/login` či `/forgot-password` vrátia 404. Na serveri nastavte vlastnú, ťažko uhádnuteľnú hodnotu (len písmená, číslice a pomlčky), nikam ju nezverejňujte a po zmene spustite `php artisan config:cache` a `php artisan route:cache`. Tajná cesta len odfiltruje automatické skenery – administrácia je chránená prihlásením. Verejná registrácia je vypnutá; prístup má len používateľ s `is_admin = 1`. Súbory k podujatiam sa ukladajú do `storage/app/public/akcie/<rok>/` a sú dostupné na `/storage/akcie/<rok>/…` – rovnaké adresy ako na starom webe (`php artisan storage:link`).

Fotografie k článkom (`storage/app/public/clanky/<rok>/`) sa pri nahraní zmenšia na max. 1600 px cez balík `intervention/image-laravel`. Server potrebuje rozšírenie PHP **GD** (alebo Imagick – `IMAGE_DRIVER` v `.env`). Bez neho sa fotografia uloží v pôvodnej veľkosti a do logu sa zapíše varovanie.

## Cookies a súhlas

Web dnes používa len nevyhnutné cookies (relácia, `XSRF-TOKEN`, uložené rozhodnutie o cookies), preto lišta so súhlasom nie je zobrazená. V pätičke je vždy odkaz **Nastavenia cookies** a stránka `/zasady-cookies`.

- Analytika: po nastavení `GOOGLE_ANALYTICS_ID=G-…` v `.env` sa automaticky zobrazí lišta so súhlasom (Prijať / Odmietnuť / Nastavenia). Google Analytics sa načíta až po súhlase; po odvolaní sa vypne a jeho cookies sa vymažú.
- Nastavenia sú v `config/cookies.php` (verzia zásad, platnosť rozhodnutia 6 mesiacov, uchovávanie záznamov 3 roky), texty kategórií v `app/Support/Cookies.php`. Pri zmene kategórií zvýšte `version`, všetci návštevníci budú požiadaní znova.
- Každé rozhodnutie sa ukladá ako dôkaz súhlasu do tabuľky `cookie_consents` (náhodné ID, kategórie, čas; bez IP adresy). Staré záznamy maže plánovač – na serveri musí bežať cron `* * * * * php artisan schedule:run`.
- Ďalší skript závislý od súhlasu pridajte ako `<script type="text/plain" data-cookie-category="analytics">` (alebo s `data-src`) – spustí sa až po súhlase.
