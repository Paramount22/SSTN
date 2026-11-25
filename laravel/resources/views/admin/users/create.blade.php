<x-admin.layout title="Pozvať administrátora">
    <x-slot:subtitle>Pozvaný dostane e-mail s odkazom, cez ktorý si sám nastaví heslo. Odkaz platí 7 dní.</x-slot:subtitle>

    @php $input = 'mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue'; @endphp

    <form method="post" action="{{ route('admin.users.store') }}" class="max-w-2xl space-y-4">
        @csrf
        <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Meno <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="off" class="{{ $input }}">
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200">E-mail <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="off" class="{{ $input }}">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>
            </div>
            <p class="mt-5 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                Nový administrátor bude mať rovnaké práva ako vy, vrátane správy ostatných administrátorov. Všetci aktívni administrátori dostanú o pozvánke e-mail.
            </p>
        </section>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-full bg-sstn-navy dark:bg-sstn-blue px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">Poslať pozvánku</button>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Zrušiť</a>
        </div>
    </form>
</x-admin.layout>
