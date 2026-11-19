@props(['name', 'value' => null, 'placeholder' => '', 'label' => 'Text'])

@php
    $btn = 'grid h-8 min-w-8 place-items-center rounded-md px-1.5 text-sm font-semibold text-slate-600 hover:bg-white hover:text-slate-900 disabled:pointer-events-none disabled:opacity-40 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white';
    $on = 'bg-white text-sstn-navy shadow-sm ring-1 ring-slate-200 dark:bg-slate-800 dark:text-sky-300 dark:ring-slate-700';
    $sep = 'mx-1 h-5 w-px bg-slate-200 dark:bg-slate-700';
@endphp

{{-- Rich text editor (TipTap, resources/js/app.js). The hidden input carries the HTML; the server sanitizes it. --}}
<div x-data="richEditor(@js((string) $value), @js($placeholder), @js($label))" {{ $attributes->merge(['class' => 'overflow-hidden rounded-lg border border-slate-300 bg-white shadow-sm focus-within:border-sstn-blue focus-within:ring-1 focus-within:ring-sstn-blue dark:border-slate-600 dark:bg-slate-800']) }}>
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" :value="content">

    <div role="toolbar" aria-label="Formátovanie textu" class="flex flex-wrap items-center gap-0.5 border-b border-slate-200 bg-slate-50 px-2 py-1.5 dark:border-slate-700 dark:bg-slate-900/60">
        <button type="button" class="{{ $btn }}" :class="isActive('paragraph') && '{{ $on }}'" :aria-pressed="isActive('paragraph')" @click="run('setParagraph')" title="Odsek">¶</button>
        <button type="button" class="{{ $btn }}" :class="isActive('heading', { level: 2 }) && '{{ $on }}'" :aria-pressed="isActive('heading', { level: 2 })" @click="run('toggleHeading', { level: 2 })" title="Nadpis">H2</button>
        <button type="button" class="{{ $btn }}" :class="isActive('heading', { level: 3 }) && '{{ $on }}'" :aria-pressed="isActive('heading', { level: 3 })" @click="run('toggleHeading', { level: 3 })" title="Podnadpis">H3</button>
        <span class="{{ $sep }}" aria-hidden="true"></span>
        <button type="button" class="{{ $btn }}" :class="isActive('bold') && '{{ $on }}'" :aria-pressed="isActive('bold')" @click="run('toggleBold')" title="Tučné (Ctrl+B)" aria-label="Tučné"><span class="font-extrabold">B</span></button>
        <button type="button" class="{{ $btn }}" :class="isActive('italic') && '{{ $on }}'" :aria-pressed="isActive('italic')" @click="run('toggleItalic')" title="Kurzíva (Ctrl+I)" aria-label="Kurzíva"><span class="font-serif italic">I</span></button>
        <button type="button" class="{{ $btn }}" :class="isActive('underline') && '{{ $on }}'" :aria-pressed="isActive('underline')" @click="run('toggleUnderline')" title="Podčiarknuté (Ctrl+U)" aria-label="Podčiarknuté"><span class="underline">U</span></button>
        <button type="button" class="{{ $btn }}" :class="isActive('strike') && '{{ $on }}'" :aria-pressed="isActive('strike')" @click="run('toggleStrike')" title="Prečiarknuté" aria-label="Prečiarknuté"><span class="line-through">S</span></button>
        <span class="{{ $sep }}" aria-hidden="true"></span>
        <button type="button" class="{{ $btn }}" :class="isActive('bulletList') && '{{ $on }}'" :aria-pressed="isActive('bulletList')" @click="run('toggleBulletList')" title="Odrážky" aria-label="Odrážky"><x-admin.icon name="list-bullet" class="h-4 w-4" /></button>
        <button type="button" class="{{ $btn }}" :class="isActive('orderedList') && '{{ $on }}'" :aria-pressed="isActive('orderedList')" @click="run('toggleOrderedList')" title="Číslovaný zoznam" aria-label="Číslovaný zoznam"><x-admin.icon name="list-number" class="h-4 w-4" /></button>
        <button type="button" class="{{ $btn }}" :class="isActive('blockquote') && '{{ $on }}'" :aria-pressed="isActive('blockquote')" @click="run('toggleBlockquote')" title="Citát" aria-label="Citát"><x-admin.icon name="quote" class="h-4 w-4" /></button>
        <button type="button" class="{{ $btn }}" :class="(isActive('link') || linkOpen) && '{{ $on }}'" :aria-pressed="isActive('link')" @click="openLink()" title="Odkaz" aria-label="Odkaz"><x-admin.icon name="link" class="h-4 w-4" /></button>
        <span class="{{ $sep }}" aria-hidden="true"></span>
        <button type="button" class="{{ $btn }}" :disabled="! can('undo')" @click="run('undo')" title="Späť (Ctrl+Z)" aria-label="Späť"><x-admin.icon name="undo" class="h-4 w-4" /></button>
        <button type="button" class="{{ $btn }}" :disabled="! can('redo')" @click="run('redo')" title="Znova (Ctrl+Y)" aria-label="Znova"><x-admin.icon name="redo" class="h-4 w-4" /></button>
    </div>

    {{-- Link address, inline instead of a browser prompt --}}
    <div x-show="linkOpen" x-cloak class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-blue-50/60 px-3 py-2 dark:border-slate-700 dark:bg-sky-500/5">
        <x-admin.icon name="link" class="h-4 w-4 text-sstn-blue dark:text-sky-400" />
        <input type="text" x-ref="linkInput" x-model="linkUrl" @keydown.enter.prevent="saveLink()" @keydown.escape.prevent="closeLink()"
               placeholder="https://… , /storage/… alebo mailto:…" aria-label="Adresa odkazu"
               class="min-w-48 flex-1 rounded-md border-slate-300 py-1.5 text-sm focus:border-sstn-blue focus:ring-sstn-blue dark:border-slate-600">
        <button type="button" @click="saveLink()" class="rounded-full bg-sstn-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#003a80] dark:bg-sstn-blue dark:hover:bg-[#0069ad]">Použiť</button>
        <button type="button" x-show="isActive('link')" @click="removeLink()" class="rounded-full px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">Odstrániť odkaz</button>
        <button type="button" @click="closeLink()" class="rounded-full px-3 py-1.5 text-xs font-medium text-slate-500 hover:bg-white dark:text-slate-400 dark:hover:bg-slate-800">Zrušiť</button>
    </div>

    <div x-ref="editor"></div>
</div>
