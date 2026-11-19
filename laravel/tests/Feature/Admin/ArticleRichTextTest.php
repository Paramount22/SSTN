<?php

use App\Models\Article;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

function storeArticle(string $body): Article
{
    test()->actingAs(test()->admin)->post(route('admin.articles.store'), [
        'title' => 'Článok '.uniqid(), 'published_on' => '2026-11-19', 'body' => $body, 'is_published' => '1',
    ])->assertSessionHasNoErrors();

    return Article::latest('id')->first();
}

test('the article form uses the rich text editor', function () {
    $this->actingAs($this->admin)->get(route('admin.articles.create'))
        ->assertOk()
        ->assertSee('richEditor(', false)
        ->assertSee('aria-label="Formátovanie textu"', false)
        ->assertDontSee('<textarea id="body"', false);
});

test('allowed formatting is kept', function () {
    $html = '<h2>Program</h2><p><strong>Tučné</strong>, <em>kurzíva</em>, <u>podčiarknuté</u>, <s>preč</s></p>'
        .'<ul><li><p>bod</p></li></ul><ol><li><p>jeden</p></li></ol><blockquote><p>citát</p></blockquote>'
        .'<p><a href="https://www.zsvts.sk/">ZSVTS</a> <a href="/storage/akcie/2026/pozvanka.pdf">pozvánka</a> <a href="mailto:radic.sstn@centrum.sk">e-mail</a></p>';

    $body = storeArticle($html)->body;

    expect($body)->toContain('<h2>Program</h2>')
        ->toContain('<strong>Tučné</strong>')
        ->toContain('<blockquote><p>citát</p></blockquote>')
        ->toContain('<a href="https://www.zsvts.sk/" rel="noopener noreferrer">ZSVTS</a>')
        ->toContain('<a href="/storage/akcie/2026/pozvanka.pdf" rel="noopener noreferrer">');
});

test('scripts, event handlers and javascript links are removed', function () {
    $body = storeArticle(
        '<p onclick="steal()">Text</p><script>alert(1)</script><img src="x" onerror="alert(2)">'
        .'<iframe src="https://evil.test"></iframe><p><a href="javascript:alert(3)">klik</a></p><style>body{display:none}</style>'
    )->body;

    expect($body)->toBe('<p>Text</p><p><a rel="noopener noreferrer">klik</a></p>')
        ->not->toContain('script')
        ->not->toContain('onerror')
        ->not->toContain('javascript:');
});

test('plain text still becomes paragraphs and an empty editor stores nothing', function () {
    expect(storeArticle("Prvý odsek & viac.\n\nDruhý odsek.")->body)->toBe('<p>Prvý odsek &amp; viac.</p><p>Druhý odsek.</p>')
        ->and(storeArticle('<p></p>')->body)->toBeNull()
        ->and(storeArticle('<p>Text</p><h2>Program</h2><p></p><p> <br> </p>')->body)->toBe('<p>Text</p><h2>Program</h2>');
});

test('the public page renders the formatting but never the stripped markup', function () {
    storeArticle('<p>Text s <strong>tučným</strong> slovom.</p><script>alert(1)</script>');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<p>Text s <strong>tučným</strong> slovom.</p>', false)
        ->assertDontSee('alert(1)', false);
});

test('the excerpt is plain text', function () {
    $article = Article::factory()->create(['body' => '<h2>Nadpis</h2><p>Text &amp; <strong>viac</strong>.</p>']);

    expect($article->excerpt)->toBe('Nadpis Text & viac.');
});
