<x-guest-layout>
    @if ($user)
        <x-slot:heading>Vitajte v administrácii</x-slot:heading>
        <x-slot:intro>Nastavte si heslo k účtu {{ $user->name }}. Potom sa ním prihlásite do administrácie webu SSTN.</x-slot:intro>

        <form method="POST" action="{{ route('invitation.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $user->email }}">

            <x-auth.field name="email_display" type="email" label="E-mail" :value="$user->email" disabled autocomplete="username" />
            <x-auth.field name="password" type="password" label="Heslo" required autofocus autocomplete="new-password" />
            <x-auth.field name="password_confirmation" type="password" label="Heslo znova" required autocomplete="new-password" />

            <x-auth.button>Nastaviť heslo</x-auth.button>
        </form>
    @else
        <x-slot:heading>Pozvánka neplatí</x-slot:heading>
        <x-slot:intro>{{ $invalid }}</x-slot:intro>

        <a href="{{ route('login') }}" class="text-sm font-medium text-sstn-blue hover:underline dark:text-sky-400">Prejsť na prihlásenie</a>
    @endif
</x-guest-layout>
