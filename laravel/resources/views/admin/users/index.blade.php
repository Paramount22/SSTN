<x-admin.layout title="Administrátori">
    <x-slot:subtitle>Kto má prístup do administrácie. O každom pridaní alebo odobratí prístupu dostanú všetci aktívni administrátori e-mail.</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-full bg-sstn-navy dark:bg-sstn-blue px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">
            <x-admin.icon name="plus" class="h-4 w-4" /> Pozvať administrátora
        </a>
    </x-slot:actions>

    @php
        $button = 'rounded-full px-3 py-1.5 text-xs font-semibold ring-1 transition';
        $neutral = $button.' text-slate-700 ring-slate-200 hover:bg-slate-50 dark:text-slate-200 dark:ring-slate-700 dark:hover:bg-slate-800';
        $danger = $button.' text-red-600 ring-red-200 hover:bg-red-50 dark:text-red-400 dark:ring-red-500/30 dark:hover:bg-red-500/10';
    @endphp

    <div class="max-w-5xl space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($users as $user)
                    @php $self = $user->is(auth()->user()); @endphp
                    <li class="flex flex-wrap items-center gap-x-4 gap-y-3 px-5 py-4 {{ $user->isActive() ? '' : 'opacity-70' }}">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-sm font-semibold text-white {{ $user->isActive() && ! $user->isInvitationPending() ? 'bg-sstn-navy dark:bg-sstn-blue' : 'bg-slate-400 dark:bg-slate-600' }}">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-slate-900 dark:text-white">
                                {{ $user->name }}
                                @if ($self)<span class="ml-1 text-xs font-normal text-slate-400 dark:text-slate-500">(vy)</span>@endif
                            </p>
                            <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                        </div>

                        {{-- Status and buttons: a second line under the name on phones, on the right otherwise --}}
                        <div class="flex w-full flex-wrap items-center gap-2 pl-14 sm:w-auto sm:pl-0">
                            @if (! $user->isActive())
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Deaktivovaný od {{ $user->deactivated_at->format('j. n. Y') }}</span>
                            @elseif ($user->isInvitationPending())
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">Pozvánka čaká · odoslaná {{ $user->invitation_sent_at->format('j. n. Y') }}</span>
                            @else
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Aktívny</span>
                            @endif

                            @if (! $user->isActive())
                                <form method="post" action="{{ route('admin.users.reactivate', $user) }}">
                                    @csrf @method('patch')
                                    <button class="{{ $neutral }}">Znova aktivovať</button>
                                </form>
                            @elseif ($user->isInvitationPending())
                                <form method="post" action="{{ route('admin.users.invitation.resend', $user) }}">
                                    @csrf
                                    <button class="{{ $neutral }}">Poslať znova</button>
                                </form>
                                <form method="post" action="{{ route('admin.users.invitation.cancel', $user) }}"
                                      data-confirm="Účet sa zmaže a odkaz z pozvánky prestane platiť." data-confirm-title="Zrušiť pozvánku?"
                                      data-confirm-item="{{ $user->name }} ({{ $user->email }})" data-confirm-action="Zrušiť pozvánku">
                                    @csrf @method('delete')
                                    <button class="{{ $danger }}">Zrušiť pozvánku</button>
                                </form>
                            @elseif (! $self)
                                <form method="post" action="{{ route('admin.users.deactivate', $user) }}"
                                      data-confirm="Účet sa okamžite odhlási a nebude sa môcť prihlásiť. Údaje zostanú zachované a prístup môžete neskôr znova aktivovať."
                                      data-confirm-title="Deaktivovať prístup?" data-confirm-item="{{ $user->name }} ({{ $user->email }})"
                                      data-confirm-action="Deaktivovať" data-confirm-undoable>
                                    @csrf @method('patch')
                                    <button class="{{ $danger }}">Deaktivovať</button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <header class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h2 class="font-semibold text-slate-900 dark:text-white">Posledné zmeny</h2>
            </header>
            @if ($activities->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Zatiaľ žiadne zmeny.</p>
            @else
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($activities as $activity)
                        <li class="grid gap-1 px-5 py-3 text-sm sm:grid-cols-[8.5rem_1fr]">
                            <span class="tabular-nums text-slate-400 dark:text-slate-500">{{ $activity->created_at->format('j. n. Y H:i') }}</span>
                            <span class="min-w-0">
                                <span class="font-medium text-slate-900 dark:text-white">{{ $activity->label() }}</span>
                                <span class="text-slate-500 dark:text-slate-400">· {{ $activity->subject_name }} ({{ $activity->subject_email }}) · vykonal(a): {{ $activity->actorLabel() }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-admin.layout>
