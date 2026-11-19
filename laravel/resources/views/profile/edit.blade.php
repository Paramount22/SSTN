<x-admin.layout title="Môj profil">
    <x-slot:subtitle>Meno, prihlasovací e-mail a heslo k administrácii.</x-slot:subtitle>

    <div class="max-w-5xl space-y-6">
        {{-- Account summary --}}
        <section class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-sstn-navy text-xl font-semibold text-white dark:bg-sstn-blue">
                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="truncate text-lg font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
            </div>
            @if ($user->is_admin)
                <span class="ml-auto shrink-0 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-sstn-blue dark:bg-sky-500/10 dark:text-sky-400">Administrátor</span>
            @endif
        </section>

        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
        @include('profile.partials.delete-user-form')
    </div>
</x-admin.layout>
