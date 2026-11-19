<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'flex w-full items-center justify-center gap-2 rounded-full bg-sstn-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#003a80] focus:outline-none focus-visible:ring-2 focus-visible:ring-sstn-blue focus-visible:ring-offset-2 dark:bg-sstn-blue dark:hover:bg-[#0069ad] dark:focus-visible:ring-offset-slate-950',
]) }}>
    {{ $slot }}
</button>
