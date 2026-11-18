<?php

use App\Models\Article;
use App\Models\Event;
use App\Models\EventAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * A real temp file whose content looks like an allowed type but whose name ends
 * in .html. Content checks alone accept it; the web server would serve it as a page.
 */
function disguisedUpload(string $name, string $content): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'upl');
    file_put_contents($path, $content);

    return new UploadedFile($path, $name, null, null, true);
}

const PDF_AS_HTML = "%PDF-1.4\n<html><script>alert(document.domain)</script></html>\n%%EOF";
// 1×1 PNG followed by HTML.
const PNG_AS_HTML = "\x89PNG\r\n\x1a\n\0\0\0\rIHDR\0\0\0\x01\0\0\0\x01\x08\x06\0\0\0\x1f\x15\xc4\x89\0\0\0\rIDATx\x9cc\xf8\x0f\0\0\x01\x01\0\x05\x18\xd8N\0\0\0\0IEND\xaeB`\x82<script>alert(1)</script>";

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    Storage::fake('public');
});

test('a PDF disguised as .html is rejected as an event attachment', function () {
    $event = Event::factory()->create();

    $this->actingAs($this->admin)->post(route('admin.events.attachments.store', $event), [
        'file' => disguisedUpload('pozvanka.html', PDF_AS_HTML),
    ])->assertSessionHasErrors('file');

    expect($event->attachments()->count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

test('a PDF disguised as .html is rejected when creating an event', function () {
    $this->actingAs($this->admin)->post(route('admin.events.store'), [
        'title' => 'Seminár', 'type' => 'sstn', 'starts_on' => '2027-01-15',
        'files' => [disguisedUpload('program.html', PDF_AS_HTML)],
    ])->assertSessionHasErrors('files.0');

    expect(Event::count())->toBe(0);
});

test('an image disguised as .html is rejected as an article photo', function () {
    $this->actingAs($this->admin)->post(route('admin.articles.store'), [
        'title' => 'Správa', 'published_on' => '2026-11-18',
        'image' => disguisedUpload('foto.html', PNG_AS_HTML),
    ])->assertSessionHasErrors('image');

    expect(Article::count())->toBe(0);
});

test('real files with allowed extensions are still accepted', function () {
    $event = Event::factory()->create(['starts_on' => '2026-11-20']);

    $this->actingAs($this->admin)->post(route('admin.events.attachments.store', $event), [
        'file' => disguisedUpload('pozvanka.pdf', PDF_AS_HTML),
    ])->assertSessionHasNoErrors();

    expect($event->attachments()->first()->path)->toBe('akcie/2026/pozvanka.pdf');
});

test('models refuse to store a file with a disallowed extension', function () {
    EventAttachment::fromUpload(Event::factory()->create(), disguisedUpload('x.html', PDF_AS_HTML));
})->throws(InvalidArgumentException::class);
