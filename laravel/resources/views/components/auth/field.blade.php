@props(['name', 'label', 'type' => 'text', 'value' => null])

@php
    $class = 'block w-full rounded-lg py-2.5 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue '
        .($errors->has($name) ? 'border-red-400 dark:border-red-500/70' : 'border-slate-300');
@endphp

<div>
    <div class="flex items-baseline justify-between gap-3">
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-200">{{ $label }}</label>
        {{ $aside ?? '' }}
    </div>

    @if ($type === 'password')
        {{-- Password with a show/hide toggle --}}
        <div class="relative mt-1.5" x-data="{ show: false }">
            <input id="{{ $name }}" name="{{ $name }}" type="password" :type="show ? 'text' : 'password'"
                   {{ $attributes->merge(['class' => $class.' pr-11']) }}>
            <button type="button" @click="show = ! show"
                    :aria-label="show ? 'Skryť heslo' : 'Zobraziť heslo'" aria-label="Zobraziť heslo"
                    class="absolute inset-y-0 right-0 flex items-center rounded-r-lg px-3 text-slate-400 hover:text-slate-600 focus:outline-none focus-visible:text-sstn-blue dark:text-slate-500 dark:hover:text-slate-300">
                <x-admin.icon name="eye" class="h-5 w-5" x-show="! show" />
                <x-admin.icon name="eye-off" class="h-5 w-5" x-show="show" x-cloak />
            </button>
        </div>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}"
               {{ $attributes->merge(['class' => $class.' mt-1.5']) }}>
    @endif

    <x-input-error :messages="$errors->get($name)" class="mt-1.5" />
</div>
