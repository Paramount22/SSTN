<section class="grid gap-6 rounded-2xl border border-red-200 bg-white p-6 shadow-sm lg:grid-cols-3 dark:border-red-500/30 dark:bg-slate-900">
    <header>
        <h2 class="font-semibold text-red-700 dark:text-red-400">Zmazanie účtu</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Účet sa zmaže natrvalo a už sa ním neprihlásite. Obsah webu, ktorý ste pridali, zostane zachovaný.</p>
    </header>

    <div class="lg:col-span-2">
        @if ($user->isLastAdmin())
            <p class="flex gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                <x-admin.icon name="users" class="h-5 w-5 shrink-0" />
                Toto je jediný administrátorský účet, preto ho nemožno zmazať. Bez neho by sa do administrácie nedalo prihlásiť.
            </p>
        @else
            <div x-data x-init="@if ($errors->userDeletion->isNotEmpty()) $refs.dialog.showModal() @endif">
                <button type="button" @click="$refs.dialog.showModal(); $nextTick(() => $refs.password.focus())"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-red-600 ring-1 ring-red-200 hover:bg-red-50 dark:bg-slate-900 dark:text-red-400 dark:ring-red-500/30 dark:hover:bg-red-500/10">
                    <x-admin.icon name="trash" class="h-4 w-4" /> Zmazať môj účet
                </button>

                {{-- Same look as the admin's delete dialog; the password confirms the deletion. --}}
                <dialog x-ref="dialog" data-confirm-dialog @click.self="$refs.dialog.close()" aria-labelledby="delete-account-title"
                        class="w-[calc(100%-2rem)] max-w-md overflow-hidden rounded-2xl bg-white p-0 text-slate-700 shadow-2xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm dark:bg-slate-900 dark:text-slate-200 dark:ring-1 dark:ring-slate-800">
                    <form method="post" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('delete')

                        <div class="flex gap-4 p-6">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                                <x-admin.icon name="trash" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <h2 id="delete-account-title" class="text-lg font-semibold text-slate-900 dark:text-white">Zmazať váš účet?</h2>
                                <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">Účet <strong class="font-medium text-slate-800 dark:text-slate-100">{{ $user->email }}</strong> sa natrvalo zmaže. Pre potvrdenie zadajte svoje heslo. Túto akciu nemožno vrátiť späť.</p>

                                <label for="delete_password" class="sr-only">Heslo</label>
                                <input id="delete_password" x-ref="password" name="password" type="password" placeholder="Heslo" autocomplete="current-password"
                                       class="mt-4 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-slate-600">
                                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
                            </div>
                        </div>

                        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end dark:border-slate-800 dark:bg-slate-800/60">
                            <button type="button" @click="$refs.dialog.close()"
                                    class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-200 dark:ring-slate-700 dark:hover:bg-slate-800">
                                Zrušiť
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                                <x-admin.icon name="trash" class="h-4 w-4" /> Zmazať účet
                            </button>
                        </div>
                    </form>
                </dialog>
            </div>
        @endif
    </div>
</section>
