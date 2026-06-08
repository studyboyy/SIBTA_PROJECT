@props([
    'message' => null,
])

@if ($message)
    <div x-data="{ visible: true, pendingSubmit: false }" x-show="visible" x-transition.opacity.duration.200ms
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
        role="alert"
        {{ $attributes->class(['mt-1.5 inline-flex max-w-full items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-medium leading-5 text-rose-700 shadow-sm shadow-rose-100/60']) }}>
        <span class="mt-0.5 inline-flex size-4 shrink-0 items-center justify-center rounded-full bg-rose-100 text-[10px] font-bold text-rose-700">
            !
        </span>
        <span class="min-w-0 whitespace-normal">{{ $message }}</span>
    </div>
@endif
