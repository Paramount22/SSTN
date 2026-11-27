# SSTN – web Slovenskej spoločnosti pre technickú normalizáciu

Verejný web (jedna úvodná stránka, akcie, články) s vlastnou administráciou.
Všeobecné pravidlá práce sú v globálnom ~/.claude/CLAUDE.md; tu sú len pravidlá tohto projektu.

## Stack
- Laravel 13, PHP 8.3, Breeze (Blade), Alpine.js, Tailwind 3, Vite
- Editor článkov: Tiptap; obrázky: intervention/image-laravel
- Testy: Pest 5; lokálne SQLite, beží na Laravel Herd
- Git repozitár je o úroveň vyššie (`SSTN/`), Laravel je v `SSTN/laravel`

## Príkazy
- Testy: `php artisan test --compact` (pod Windows/Herd: `C:\Users\juric\.config\herd\bin\php.bat artisan test --compact`; v Bash nie je `php` v PATH)
- Frontend: `npm run build` (vývoj: `npm run dev`)
- Formátovanie: `vendor/bin/pint` len na súbory, ktoré si menil
- Admin účet: `php artisan admin:create` (heslo sa zadáva interaktívne)

## Štruktúra
- Verejný web: `resources/views/site/`, controller `HomeController`
- Administrácia: `app/Http/Controllers/Admin/`, `resources/views/admin/`, Form Requests v `app/Http/Requests/Admin/`
- Pomocné triedy: `app/Support/` (RichText, Assets, Cookies)

## Bezpečnostné pravidlá (neporušovať)
- `is_admin`, `deactivated_at`, `invitation_sent_at` nikdy nepridávať do `#[Fillable]`; nastavovať len cez `forceFill()`.
- Nové admin routy patria do skupiny s middleware `['auth', 'admin']` a Form Request musí mať `authorize()` s kontrolou `is_admin`.
- Administrácia, login a heslá bežia pod `config('admin.path')` (ADMIN_PATH v .env). Nikdy nevytváraj natvrdo `/admin` ani `/login` a v kóde nepoužívaj URL, iba `route()`.
- HTML z editora ukladať len cez `RichText::sanitize()` (mutator na modeli). `{!! !!}` len pre takto vyčistený obsah alebo pre text, ktorý prešiel cez `e()`.
- Nahrávanie súborov: prílohy len s príponami z `EventAttachment::ALLOWED_EXTENSIONS`, obrázky podľa `Article::IMAGE_TYPES`; kontrola typu aj prípony (`File::types()->extensions()`).
- Každá zmena prístupu adminov sa zapisuje cez `AdminActivity::record()`.
- Žiadne heslá ani admin účty v seederoch, migráciách ani v gite.
- Admin stránky a profil majú zostať `noindex`.

## Konvencie
- Všetky texty v UI, hlášky a validačné správy po slovensky (`lang/sk/`, `lang/sk.json`); názvy polí pre validáciu cez `attributes()` vo Form Requeste.
- Komentáre v kóde po anglicky, krátke, vysvetľujú „prečo“.
- Dátumy na webe v slovenskom formáte (vzor: `Event::getDateLongAttribute()`).
- Pri potvrdení zmazania použiť štýlovaný dialóg z administrácie, nie `confirm()`.
- Súbory z `public/` linkovať cez `Assets::url()` (cache busting).

## Workflow
- Ku každej zmene správania pridaj alebo uprav Pest test v `tests/Feature/`; pred ukončením spusti celú sadu testov.
- Nemeň existujúce migrácie; zmeny schémy vždy ako nová migrácia.
- Pred väčšou zmenou (nová tabuľka, nová sekcia administrácie) najprv navrhni plán a počkaj na schválenie.

## Produkcia (na zapamätanie)
- Potrebný cron pre Scheduler (`model:prune` maže staré súhlasy s cookies – GDPR).
- Reálny SMTP (pozvánky adminov a obnova hesla), `APP_DEBUG=false`, nastavená `ADMIN_PATH`, `php artisan storage:link`.
- Prílohy na `public` disku sú dostupné aj pre nepublikované akcie – neukladať tam interné dokumenty.
