<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    Storage::fake('public');
});

test('admin can create an event with attachments', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
        'title' => 'Stretnutie normalizátorov Slovenska 2027',
        'type' => 'sstn',
        'starts_on' => '2027-11-19',
        'location' => 'Dom ZSVTS, Bratislava',
        'is_published' => '1',
        'files' => [UploadedFile::fake()->create('Pozvánka SNS 2027.pdf', 120, 'application/pdf')],
    ]);

    $event = Event::firstWhere('slug', 'stretnutie-normalizatorov-slovenska-2027');

    expect($event)->not->toBeNull()
        ->and($event->attachments)->toHaveCount(1)
        ->and($event->attachments->first()->path)->toBe('akcie/2027/pozvanka_sns_2027.pdf')
        ->and($event->attachments->first()->label)->toBe('Pozvanka Sns 2027');

    Storage::disk('public')->assertExists('akcie/2027/pozvanka_sns_2027.pdf');
    $response->assertRedirect(route('admin.events.edit', $event, absolute: false));
});

test('validation rejects an end date before the start date', function () {
    $this->actingAs($this->admin)->post(route('admin.events.store'), [
        'title' => 'X', 'type' => 'sstn', 'starts_on' => '2027-05-10', 'ends_on' => '2027-05-01',
    ])->assertSessionHasErrors('ends_on');
});

test('admin can update and delete an event', function () {
    $event = Event::factory()->create(['title' => 'Pôvodný názov']);

    $this->actingAs($this->admin)->put(route('admin.events.update', $event), [
        'title' => 'Nový názov', 'type' => 'zsvts', 'starts_on' => '2026-05-05', 'is_published' => '0',
    ])->assertRedirect();

    expect($event->fresh())->title->toBe('Nový názov')->is_published->toBeFalse();

    $this->actingAs($this->admin)->delete(route('admin.events.destroy', $event))
        ->assertRedirect(route('admin.events.index', absolute: false));

    expect(Event::find($event->id))->toBeNull();
});

test('attachment can be added, renamed and removed', function () {
    $event = Event::factory()->create(['starts_on' => '2026-03-01']);

    $this->actingAs($this->admin)->post(route('admin.events.attachments.store', $event), [
        'label' => 'Program', 'file' => UploadedFile::fake()->create('program.docx', 50),
    ])->assertRedirect();

    $attachment = $event->attachments()->first();
    expect($attachment->label)->toBe('Program')->and($attachment->path)->toBe('akcie/2026/program.docx');

    $this->actingAs($this->admin)->patch(route('admin.attachments.update', $attachment), ['label' => 'Program seminára', 'sort' => 2]);
    expect($attachment->fresh()->label)->toBe('Program seminára');

    $this->actingAs($this->admin)->delete(route('admin.attachments.destroy', $attachment));
    Storage::disk('public')->assertMissing('akcie/2026/program.docx');
    expect($event->attachments()->count())->toBe(0);
});

test('upcoming and past scopes split events by date', function () {
    Event::factory()->upcoming()->count(2)->create();
    Event::factory()->past()->count(3)->create();

    expect(Event::upcoming()->count())->toBe(2)->and(Event::past()->count())->toBe(3);
});

test('non-admins cannot manage events', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('admin.events.store'), ['title' => 'x', 'type' => 'sstn', 'starts_on' => '2026-01-01'])
        ->assertForbidden();
});
