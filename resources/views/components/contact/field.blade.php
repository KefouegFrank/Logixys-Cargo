{{--
    Filled control with a leading icon and placeholder-only styling, as on the reference.
    The label is still rendered for screen readers and autofill — a placeholder alone
    leaves the field unnamed once a value is typed.
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'options' => [],
    'rows' => 5,
    'icon' => null,
    'autocomplete' => null,
])

@php
    $id = 'contact-'.$name;
    $error = $errors->first($name);
    $described = $error ? "{$id}-error" : null;

    $control = 'w-full rounded-field border-0 bg-navy-50 py-3.5 pl-11 pr-4 text-sm text-ink '
        .'transition-colors duration-200 placeholder:text-ink-subtle hover:bg-navy-100 '
        .'focus:bg-white focus:outline-2 focus:outline-offset-0 focus:outline-focus'
        .($error ? ' ring-1 ring-inset ring-danger-border' : '');
@endphp

<div>
    <label for="{{ $id }}" class="sr-only">
        {{ $label }} ({{ $required ? __('contact.form.required') : __('contact.form.optional') }})
    </label>

    <div class="relative">
        <span class="pointer-events-none absolute left-4 top-4 text-ink-subtle">
            <x-dynamic-component :component="$icon" class="h-4 w-4" aria-hidden="true" />
        </span>

        @if ($type === 'textarea')
            <textarea
                id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
                @if ($required) required @endif
                @if ($error) aria-invalid="true" aria-describedby="{{ $described }}" @endif
                placeholder="{{ $label }}"
                class="{{ $control }} resize-y"
            >{{ old($name) }}</textarea>
        @elseif ($type === 'select')
            <select
                id="{{ $id }}" name="{{ $name }}"
                @if ($required) required @endif
                @if ($error) aria-invalid="true" aria-describedby="{{ $described }}" @endif
                class="{{ $control }} appearance-none pr-10 {{ old($name) ? '' : 'text-ink-subtle' }}"
            >
                <option value="" @selected(blank(old($name)))>{{ $label }}</option>
                @foreach ($options as $value => $optionLabel)
                    <option value="{{ $value }}" @selected(old($name) === (string) $value)>{{ $optionLabel }}</option>
                @endforeach
            </select>
            <x-heroicon-m-chevron-down class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-subtle" aria-hidden="true" />
        @else
            <input
                id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}"
                @if ($required) required @endif
                @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                @if ($error) aria-invalid="true" aria-describedby="{{ $described }}" @endif
                placeholder="{{ $label }}"
                class="{{ $control }}"
            >
        @endif
    </div>

    @if ($error)
        <p id="{{ $described }}" class="mt-1.5 text-sm text-danger-fg">{{ $error }}</p>
    @endif
</div>
