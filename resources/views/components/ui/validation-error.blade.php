@props([
    'message' => null,
])

@if ($message)
    <p x-data="{ visible: true, pendingSubmit: false }" x-show="visible" x-transition.opacity.duration.200ms
        x-init="const fieldScope = $el.parentElement;
        const form = $el.closest('form');
        const onFieldInput = () => {
            visible = false;
            pendingSubmit = false;
        };
        const onFormSubmit = () => {
            visible = false;
            pendingSubmit = true;
        };
        const observer = new MutationObserver(() => {
            if (pendingSubmit) {
                visible = true;
                pendingSubmit = false;
            }
        });

        if (fieldScope) {
            fieldScope.addEventListener('input', onFieldInput);
            fieldScope.addEventListener('change', onFieldInput);
        }

        if (form) {
            form.addEventListener('submit', onFormSubmit);
        }

        observer.observe($el, { attributes: true, attributeFilter: ['data-validation-render'] });"
        data-validation-render="{{ uniqid('validation-error-', true) }}"
        {{ $attributes->class(['mt-1 inline-flex rounded-full border border-rose-200 bg-rose-50/80 px-2.5 py-1 text-xs font-medium text-rose-700']) }}>
        {{ $message }}
    </p>
@endif
