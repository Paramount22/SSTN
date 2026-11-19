<x-guest-layout>
    <x-slot:heading>Nové heslo</x-slot:heading>
    <x-slot:intro>Nastavte si nové heslo k účtu. Potom sa ním prihlásite do administrácie.</x-slot:intro>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-auth.field name="email" type="email" label="E-mail" :value="$request->email" required autofocus autocomplete="username" />
        <x-auth.field name="password" type="password" label="Nové heslo" required autocomplete="new-password" />
        <x-auth.field name="password_confirmation" type="password" label="Nové heslo znova" required autocomplete="new-password" />

        <x-auth.button>Uložiť nové heslo</x-auth.button>
    </form>
</x-guest-layout>
