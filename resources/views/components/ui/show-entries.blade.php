@props([
    'label' => 'Show entries',
])

<label class="inline-flex items-center gap-2 text-sm text-slate-600">
    <span class="font-medium">{{ $label }}</span>
    <select
        {{ $attributes->class(['rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700']) }}>
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="15">15</option>
        <option value="20">20</option>
    </select>
</label>
