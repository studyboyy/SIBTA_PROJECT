@props([
    'label' => 'Tampil',
])

<label class="inline-flex items-center gap-2 text-sm text-slate-500">
    <span>{{ $label }}</span>
    <select
        {{ $attributes->class(['rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100']) }}>
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="15">15</option>
        <option value="20">20</option>
    </select>
    <span>entri</span>
</label>
