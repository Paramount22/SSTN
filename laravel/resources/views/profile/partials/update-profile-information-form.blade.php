@php $input = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue dark:border-slate-600'; @endphp

<section class="grid gap-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:grid-cols-3 dark:border-slate-800 dark:bg-slate-900">
    <header>
        <h2 class="font-semibold text-slate-900 dark:text-white">Údaje o účte</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Meno sa zobrazuje v administrácii. E-mailom sa prihlasujete a chodia naň odkazy na zmenu hesla.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5 lg:col-span-2">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Meno <span class="text-red-500 dark:text-red-400">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name" class="{{ $input }}">
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200">E-mail <span class="text-red-500 dark:text-red-400">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="{{ $input }}">
            <x-input-error class="mt-1" :messages="$errors->get('email')" />
        </div>

        <button type="submit" class="rounded-full bg-sstn-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:bg-sstn-blue dark:hover:bg-[#0069ad]">Uložiť údaje</button>
    </form>
</section>
