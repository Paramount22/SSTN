<x-admin.layout :title="$link->label">
    <x-slot:subtitle>{{ $link->group_label }}</x-slot:subtitle>
    <form method="post" action="{{ route('admin.links.update', $link) }}" class="max-w-2xl space-y-4">
        @csrf @method('put')
        <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">@include('admin.links._form')</section>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-full bg-sstn-navy dark:bg-sstn-blue px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">Uložiť</button>
            <a href="{{ route('admin.links.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Zrušiť</a>
        </div>
    </form>
</x-admin.layout>
