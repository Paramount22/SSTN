@php $input = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue dark:border-slate-600'; @endphp

<section class="grid gap-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:grid-cols-3 dark:border-slate-800 dark:bg-slate-900">
    <header>
        <h2 class="font-semibold text-slate-900 dark:text-white">Zmena hesla</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Použite dlhé heslo, ktoré nepoužívate nikde inde. Najbezpečnejšie je nechať ho vygenerovať správcom hesiel.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5 lg:col-span-2">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Súčasné heslo</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="{{ $input }}">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="update_password_password" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nové heslo</label>
                <input id="update_password_password" name="password" type="password" autocomplete="new-password" class="{{ $input }}">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
            </div>
            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nové heslo znova</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="{{ $input }}">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <button type="submit" class="rounded-full bg-sstn-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:bg-sstn-blue dark:hover:bg-[#0069ad]">Zmeniť heslo</button>
    </form>
</section>
