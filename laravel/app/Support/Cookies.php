<?php

namespace App\Support;

/**
 * The cookies the public site uses, grouped into consent categories. Optional
 * categories appear only while one of their services is configured.
 */
final class Cookies
{
    /** @return array<string, array{label: string, description: string, required: bool, cookies: list<array{name: string, provider: string, purpose: string, duration: string}>}> */
    public static function categories(): array
    {
        $session = self::duration((int) config('session.lifetime'), 'minutes');

        $categories = [
            'necessary' => [
                'label' => 'Nevyhnutné',
                'description' => 'Zabezpečujú základné fungovanie stránky, ochranu formulárov a zapamätanie vášho rozhodnutia o cookies. Bez nich stránka nefunguje správne, preto ich nemožno vypnúť.',
                'required' => true,
                'cookies' => [
                    ['name' => (string) config('session.cookie'), 'provider' => 'SSTN', 'purpose' => 'Relácia návštevníka, napríklad prihlásenie do administrácie.', 'duration' => $session],
                    ['name' => 'XSRF-TOKEN', 'provider' => 'SSTN', 'purpose' => 'Ochrana formulárov pred podvrhnutými požiadavkami (CSRF).', 'duration' => $session],
                    ['name' => (string) config('cookies.cookie'), 'provider' => 'SSTN', 'purpose' => 'Uchováva vaše rozhodnutie o cookies.', 'duration' => self::duration((int) config('cookies.lifetime_days'), 'days')],
                ],
            ],
        ];

        if (filled(config('cookies.google_analytics_id'))) {
            $categories['analytics'] = [
                'label' => 'Analytické',
                'description' => 'Pomáhajú nám zistiť, koľko ľudí stránku navštevuje a ktoré časti ich zaujímajú, aby sme ju mohli zlepšovať. Údaje zbiera služba Google Analytics 4 spoločnosti Google Ireland Limited.',
                'required' => false,
                'cookies' => [
                    ['name' => '_ga', 'provider' => 'Google', 'purpose' => 'Rozlišuje jednotlivých návštevníkov.', 'duration' => '2 roky'],
                    ['name' => '_ga_*', 'provider' => 'Google', 'purpose' => 'Uchováva stav relácie pre Google Analytics 4.', 'duration' => '2 roky'],
                ],
            ];
        }

        return $categories;
    }

    /** @return list<string> keys of the categories that need consent */
    public static function optional(): array
    {
        return array_keys(array_filter(self::categories(), fn (array $category) => ! $category['required']));
    }

    /** Settings for cookie-consent.js. */
    public static function clientConfig(): array
    {
        $optional = array_intersect_key(self::categories(), array_flip(self::optional()));

        return [
            'cookie' => config('cookies.cookie'),
            'version' => (int) config('cookies.version'),
            'days' => (int) config('cookies.lifetime_days'),
            'categories' => array_keys($optional),
            // Cookie names to delete when a category is withdrawn ("_ga_*" = prefix).
            'cookies' => array_map(fn (array $category) => array_column($category['cookies'], 'name'), $optional),
            // window flags that stop a service before its cookies are deleted (Google's opt-out flag).
            'disable' => isset($optional['analytics']) ? ['analytics' => ['ga-disable-'.config('cookies.google_analytics_id')]] : [],
            'endpoint' => route('cookies.consent'),
        ];
    }

    /** "2 hodiny", "6 mesiacov", "3 roky" with Slovak plural forms. */
    public static function duration(int $amount, string $unit): string
    {
        [$amount, $forms] = match (true) {
            $unit === 'minutes' && $amount % 60 === 0 => [$amount / 60, ['hodina', 'hodiny', 'hodín']],
            $unit === 'minutes' => [$amount, ['minúta', 'minúty', 'minút']],
            $amount % 365 === 0 => [$amount / 365, ['rok', 'roky', 'rokov']],
            $amount % 30 === 0 => [$amount / 30, ['mesiac', 'mesiace', 'mesiacov']],
            default => [$amount, ['deň', 'dni', 'dní']],
        };

        return $amount.' '.match (true) {
            $amount === 1 => $forms[0],
            $amount >= 2 && $amount <= 4 => $forms[1],
            default => $forms[2],
        };
    }
}
