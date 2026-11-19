<x-guest-layout>
    <x-slot:heading>Prihlásenie</x-slot:heading>
    <x-slot:intro>Prihláste sa do správy obsahu webu Slovenskej spoločnosti pre technickú normalizáciu.</x-slot:intro>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-auth.field name="email" type="email" label="E-mail" required autofocus autocomplete="username" />

        <x-auth.field name="password" type="password" label="Heslo" required autocomplete="current-password">
            @if (Route::has('password.request'))
                <x-slot:aside>
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-sstn-blue hover:underline dark:text-sky-400">Zabudli ste heslo?</a>
                </x-slot:aside>
            @endif
        </x-auth.field>

        <label for="remember_me" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-sstn-blue focus:ring-sstn-blue dark:border-slate-600 dark:bg-slate-800">
            Zapamätať si ma na tomto zariadení
        </label>

        <x-auth.button>Prihlásiť sa</x-auth.button>
    </form>
</x-guest-layout>
