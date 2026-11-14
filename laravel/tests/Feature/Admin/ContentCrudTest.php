<?php

use App\Models\Link;
use App\Models\MembershipFee;
use App\Models\TextBlock;
use App\Models\User;

beforeEach(fn () => $this->admin = User::factory()->admin()->create());

test('membership fee crud', function () {
    $this->actingAs($this->admin)->post(route('admin.membership-fees.store'), [
        'kind' => 'Čestné', 'price' => '0', 'votes' => 1, 'is_featured' => '0', 'sort' => 5,
    ])->assertRedirect(route('admin.membership-fees.index', absolute: false));

    $fee = MembershipFee::firstWhere('kind', 'Čestné');
    expect($fee)->not->toBeNull();

    $this->actingAs($this->admin)->put(route('admin.membership-fees.update', $fee), [
        'kind' => 'Čestné členstvo', 'price' => '12,50', 'votes' => 2, 'is_featured' => '1',
    ]);
    expect($fee->fresh())->kind->toBe('Čestné členstvo')->price->toBe('12.50')->is_featured->toBeTrue();

    $this->actingAs($this->admin)->delete(route('admin.membership-fees.destroy', $fee));
    expect(MembershipFee::find($fee->id))->toBeNull();
});

test('link crud and url validation', function () {
    $this->actingAs($this->admin)->post(route('admin.links.store'), [
        'group' => 'footer', 'label' => 'Bad', 'url' => 'javascript:alert(1)',
    ])->assertSessionHasErrors('url');

    $this->actingAs($this->admin)->post(route('admin.links.store'), [
        'group' => 'footer', 'label' => 'ZSVTS', 'url' => 'https://www.zsvts.sk/',
    ])->assertRedirect();

    $link = Link::firstWhere('label', 'ZSVTS');
    $this->actingAs($this->admin)->put(route('admin.links.update', $link), [
        'group' => 'partner', 'label' => 'ZSVTS', 'url' => 'https://www.zsvts.sk/', 'description' => 'Zväz',
    ]);
    expect($link->fresh())->group->toBe('partner')->description->toBe('Zväz');

    $this->actingAs($this->admin)->delete(route('admin.links.destroy', $link));
    expect(Link::find($link->id))->toBeNull();
});

test('text blocks can be edited but not created or deleted', function () {
    $block = TextBlock::create(['key' => 'contact.email', 'label' => 'E-mail', 'type' => 'text', 'value' => 'a@b.sk']);

    $this->actingAs($this->admin)->get(route('admin.text-blocks.index'))->assertOk()->assertSee('E-mail');

    $this->actingAs($this->admin)->put(route('admin.text-blocks.update', $block), ['value' => 'radic.sstn@centrum.sk'])
        ->assertRedirect(route('admin.text-blocks.index', absolute: false));
    expect(TextBlock::get('contact.email'))->toBe('radic.sstn@centrum.sk');

    $this->actingAs($this->admin)->delete(route('admin.text-blocks.index').'/'.$block->id)->assertMethodNotAllowed();
});
