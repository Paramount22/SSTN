<x-guest-layout>
    <x-slot:heading>Overte e-mail</x-slot:heading>
    <x-slot:intro>Na váš e-mail sme poslali odkaz na overenie adresy. Ak správa neprišla, pošleme vám novú.</x-slot:intro>

    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mb-6" status="Nový overovací odkaz sme poslali na váš e-mail." />
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <x-auth.button>Poslať overovací e-mail znova</x-auth.button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-900 hover:underline dark:text-slate-400 dark:hover:text-white">Odhlásiť sa</button>
    </form>
</x-guest-layout>
