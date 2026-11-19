<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/** A 3×2 PNG – drawn with GD when available, otherwise a pre-built one. */
function fakePhoto(string $name = 'foto.png'): UploadedFile
{
    if (extension_loaded('gd')) {
        $canvas = imagecreatetruecolor(3, 2);
        ob_start();
        imagepng($canvas);
        $png = ob_get_clean();
    } else {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAMAAAACCAIAAAASFvFNAAAAEklEQVR4nGP4z8DAwMDAwMAAAAm+Af/vBIb3AAAAAElFTkSuQmCC');
    }

    return UploadedFile::fake()->createWithContent($name, $png);
}

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    Storage::fake('public');
});

test('admin can create an article with a photo', function () {
    $this->actingAs($this->admin)->post(route('admin.articles.store'), [
        'title' => 'Seminár Třinec 2024',
        'published_on' => '2024-06-13',
        'body' => "Prvý odsek.\n\nDruhý odsek.",
        'image' => fakePhoto('trinec.png'),
        'image_alt' => 'Účastníci seminára',
        'is_published' => '1',
    ])->assertRedirect();

    $article = Article::firstWhere('slug', 'seminar-trinec-2024');

    expect($article)->not->toBeNull()
        ->and($article->image_path)->toBe('clanky/2024/seminar_trinec_2024.png')
        ->and($article->image_width)->toBe(3)
        ->and($article->image_height)->toBe(2)
        ->and($article->body)->toBe('<p>Prvý odsek.</p><p>Druhý odsek.</p>');

    Storage::disk('public')->assertExists('clanky/2024/seminar_trinec_2024.png');
});

test('article can be created without a photo', function () {
    $this->actingAs($this->admin)->post(route('admin.articles.store'), [
        'title' => 'Oznam', 'published_on' => '2026-11-15',
    ])->assertSessionHasNoErrors();

    expect(Article::first()->image_path)->toBeNull();
});

test('non-image files are rejected', function () {
    $this->actingAs($this->admin)->post(route('admin.articles.store'), [
        'title' => 'X', 'published_on' => '2026-11-15',
        'image' => UploadedFile::fake()->create('pozvanka.pdf', 20, 'application/pdf'),
    ])->assertSessionHasErrors('image');
});

test('replacing the photo deletes the old file', function () {
    $article = Article::factory()->create(['title' => 'Exkurzia', 'published_on' => '2024-06-13']);
    $article->attachImage(fakePhoto('a.png'));
    $old = $article->image_path;

    $this->actingAs($this->admin)->put(route('admin.articles.update', $article), [
        'title' => 'Exkurzia', 'published_on' => '2024-06-13', 'image' => fakePhoto('b.png'), 'is_published' => '1',
    ])->assertRedirect();

    Storage::disk('public')->assertExists($article->fresh()->image_path);
    expect($article->fresh()->image_path)->toBe($old);   // same name, file replaced
});

test('photo can be removed and article deleted', function () {
    $article = Article::factory()->create(['published_on' => '2024-06-13']);
    $article->attachImage(fakePhoto());
    $path = $article->image_path;

    $this->actingAs($this->admin)->put(route('admin.articles.update', $article), [
        'title' => $article->title, 'published_on' => '2024-06-13', 'remove_image' => '1', 'is_published' => '1',
    ]);
    expect($article->fresh()->image_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);

    $article->fresh()->attachImage(fakePhoto());
    $path = $article->fresh()->image_path;
    $this->actingAs($this->admin)->delete(route('admin.articles.destroy', $article))->assertRedirect();
    expect(Article::find($article->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('articles index and forms render', function () {
    Article::factory()->count(2)->create();

    $this->actingAs($this->admin)->get(route('admin.articles.index'))->assertOk()->assertSee('Nový článok');
    $this->actingAs($this->admin)->get(route('admin.articles.create'))->assertOk()->assertSee('Fotografia');
    $this->actingAs($this->admin)->get(route('admin.articles.edit', Article::first()))->assertOk();
});

test('non-admins cannot manage articles', function () {
    $this->actingAs(User::factory()->create())->get(route('admin.articles.index'))->assertForbidden();
});

test('large photos are scaled down to the maximum side', function () {
    $canvas = imagecreatetruecolor(3200, 2000);
    imagefill($canvas, 0, 0, imagecolorallocate($canvas, 0, 128, 208));
    ob_start();
    imagejpeg($canvas, null, 90);
    $jpeg = ob_get_clean();

    $this->actingAs($this->admin)->post(route('admin.articles.store'), [
        'title' => 'Veľká fotka', 'published_on' => '2026-11-16',
        'image' => UploadedFile::fake()->createWithContent('velka.jpeg', $jpeg),
    ])->assertSessionHasNoErrors();

    $article = Article::first();
    expect($article->image_path)->toBe('clanky/2026/velka_fotka.jpg')
        ->and($article->image_width)->toBe(1600)
        ->and($article->image_height)->toBe(1000);

    [$w, $h] = getimagesize(Storage::disk('public')->path($article->image_path));
    expect([$w, $h])->toBe([1600, 1000]);
})->skip(! extension_loaded('gd'), 'GD extension required');
