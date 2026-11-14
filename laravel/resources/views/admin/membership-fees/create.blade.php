<x-admin.layout title="Nový druh členstva">
    <form method="post" action="{{ route('admin.membership-fees.store') }}" class="max-w-2xl space-y-4">
        @csrf
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">@include('admin.membership-fees._form')</section>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-full bg-sstn-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80]">Pridať</button>
            <a href="{{ route('admin.membership-fees.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Zrušiť</a>
        </div>
    </form>
</x-admin.layout>
