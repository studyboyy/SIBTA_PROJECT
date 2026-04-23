@props([
    'message' => null,
])

@if ($message)
    <p x-data="{ visible: true }" x-show="visible" x-transition.opacity.duration.200ms x-init="const fieldScope = $el.parentElement;
    const onFieldInput = () => { visible = false; };
    
    if (fieldScope) {
        fieldScope.addEventListener('input', onFieldInput);
        fieldScope.addEventListener('change', onFieldInput);
    }"
        x-on:submit.window="if ($event.target.closest('form') && $event.target.closest('form') === $el.closest('form')) visible = true"
        {{ $attributes->class(['mt-1 inline-flex rounded-full border border-rose-200 bg-rose-50/80 px-2.5 py-1 text-xs font-medium text-rose-700']) }}>
        {{ $message }}
    </p>
@endif
