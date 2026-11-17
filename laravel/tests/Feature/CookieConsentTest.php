<?php

use App\Models\CookieConsent;
use App\Support\Cookies;
use Illuminate\Support\Str;

function consent(array $overrides = []): array
{
    return array_merge(['consent_id' => (string) Str::uuid(), 'version' => config('cookies.version'), 'categories' => []], $overrides);
}

test('without optional services there is no banner, but settings and policy are available', function () {
    config(['cookies.google_analytics_id' => null]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('data-cc-banner', false)
        ->assertSee('data-cc-open', false)
        ->assertSee(route('cookies.policy'))
        ->assertDontSee('googletagmanager.com');

    $this->get(route('cookies.policy'))
        ->assertOk()
        ->assertSee(config('session.cookie'))
        ->assertSee('XSRF-TOKEN')
        ->assertSee(config('cookies.cookie'))
        ->assertSee('2 hodiny')
        ->assertSee('Momentálne nepoužívame analytické');
});

test('with Google Analytics the banner asks for consent and the scripts stay inert', function () {
    config(['cookies.google_analytics_id' => 'G-TEST123']);

    $html = $this->get(route('home'))
        ->assertSee('data-cc-banner', false)
        ->assertSeeInOrder(['Odmietnuť všetky', 'Prijať všetky', 'Nastavenia'])
        ->assertSee('data-cc-category="analytics"', false)
        ->getContent();

    // Every Google script is blocked until consent; nothing runs or loads up front.
    preg_match_all('#<script[^>]*>[^<]*?(?:googletagmanager|gtag\()#', $html, $scripts);
    expect($scripts[0])->not->toBeEmpty();
    foreach ($scripts[0] as $tag) {
        expect($tag)->toContain('type="text/plain"')->toContain('data-cookie-category="analytics"');
    }
    expect($html)->not->toMatch('#\ssrc="https://www\.googletagmanager\.com#')
        ->and($html)->not->toMatch('/<input[^>]*data-cc-category[^>]*checked/');

    $this->get(route('cookies.policy'))->assertSee('_ga_*')->assertSee('Google Analytics 4');
});

test('a consent decision is recorded as proof', function () {
    config(['cookies.google_analytics_id' => 'G-TEST123']);
    $id = (string) Str::uuid();

    $this->postJson(route('cookies.consent'), consent(['consent_id' => $id, 'categories' => ['analytics']]))->assertNoContent();
    $this->postJson(route('cookies.consent'), consent(['consent_id' => $id, 'categories' => []]))->assertNoContent();

    $records = CookieConsent::where('consent_id', $id)->orderBy('id')->get();
    expect($records)->toHaveCount(2)
        ->and($records[0]->categories)->toBe(['analytics'])
        ->and($records[1]->categories)->toBe([])
        ->and($records[1]->version)->toBe(config('cookies.version'));
});

test('unknown categories, inactive services and stale versions are rejected', function () {
    config(['cookies.google_analytics_id' => null]);

    $this->postJson(route('cookies.consent'), consent(['categories' => ['analytics']]))->assertUnprocessable();
    $this->postJson(route('cookies.consent'), consent(['categories' => ['marketing']]))->assertUnprocessable();
    $this->postJson(route('cookies.consent'), consent(['version' => 999]))->assertUnprocessable();
    $this->postJson(route('cookies.consent'), consent(['consent_id' => 'not-a-uuid']))->assertUnprocessable();

    expect(CookieConsent::count())->toBe(0);
});

test('old consent records are pruned after the retention period', function () {
    $old = CookieConsent::create(consent());
    $old->forceFill(['created_at' => now()->subDays(config('cookies.log_retention_days') + 1)])->save();
    $recent = CookieConsent::create(consent());

    $this->artisan('model:prune', ['--model' => [CookieConsent::class]])->assertSuccessful();

    expect(CookieConsent::find($old->id))->toBeNull()
        ->and(CookieConsent::find($recent->id))->not->toBeNull();
});

test('durations use Slovak plural forms', function () {
    expect(Cookies::duration(120, 'minutes'))->toBe('2 hodiny')
        ->and(Cookies::duration(60, 'minutes'))->toBe('1 hodina')
        ->and(Cookies::duration(300, 'minutes'))->toBe('5 hodín')
        ->and(Cookies::duration(180, 'days'))->toBe('6 mesiacov')
        ->and(Cookies::duration(1095, 'days'))->toBe('3 roky');
});
