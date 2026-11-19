<x-guest-layout>
    <x-slot:heading>Potvrďte heslo</x-slot:heading>
    <x-slot:intro>Ide o chránenú časť administrácie. Pred pokračovaním zadajte svoje heslo.</x-slot:intro>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <x-auth.field name="password" type="password" label="Heslo" required autofocus autocomplete="current-password" />

        <x-auth.button>Potvrdiť</x-auth.button>
    </form>
</x-guest-layout>
