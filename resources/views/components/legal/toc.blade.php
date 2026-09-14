@props(['items'])

{{-- Sticky sidebar nav, the standard shape for a long reference document —
     not the inline summary box a short page would use. --}}
<nav aria-label="{{ __('legal.summary') }}" class="lg:sticky lg:top-28">
    <p class="font-heading text-xs font-bold uppercase tracking-[0.2em] text-ink">{{ __('legal.summary') }}</p>
    <ol class="mt-3 space-y-0.5 border-l border-line text-sm">
        @foreach ($items as $number => $title)
            <li>
                <a
                    href="#art-{{ $number }}"
                    class="-ml-px block border-l-2 border-transparent py-1.5 pl-4 text-ink-muted transition-colors duration-200 hover:border-navy-400 hover:text-navy-900"
                >
                    <span class="text-ink-subtle">{{ $number }}.</span> {{ $title }}
                </a>
            </li>
        @endforeach
    </ol>
</nav>
