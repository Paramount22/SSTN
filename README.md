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

Seeder vytvorí lokálny administrátorský účet `admin@sstn.test` (heslo je v `database/seeders/DatabaseSeeder.php`) a naplní obsah z pôvodného webu (podujatia 2025–2026 s PDF, texty, členské príspevky, odkazy).

Administrácia beží na `/admin` (prihlásenie na `/login`). Verejná registrácia je vypnutá; prístup má len používateľ s `is_admin = 1`. Súbory k podujatiam sa ukladajú do `storage/app/public/akcie/<rok>/` a sú dostupné na `/storage/akcie/<rok>/…` – rovnaké adresy ako na starom webe (`php artisan storage:link`).
