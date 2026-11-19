<x-guest-layout>
    <x-slot:heading>Zabudnuté heslo</x-slot:heading>
    <x-slot:intro>Zadajte e-mail, ktorým sa prihlasujete. Pošleme vám naň odkaz, cez ktorý si nastavíte nové heslo.</x-slot:intro>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <x-auth.field name="email" type="email" label="E-mail" required autofocus autocomplete="username" />

        <x-auth.button>Poslať odkaz na zmenu hesla</x-auth.button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
        Spomenuli ste si? <a href="{{ route('login') }}" class="font-medium text-sstn-blue hover:underline dark:text-sky-400">Prihláste sa</a>
    </p>
</x-guest-layout>
