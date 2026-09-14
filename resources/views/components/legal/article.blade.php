@props(['number', 'title'])

<section id="art-{{ $number }}" class="mt-10 scroll-mt-28">
    <h2 class="font-heading text-xl font-bold text-ink">{{ $number }}. {{ $title }}</h2>
    <div class="mt-3 space-y-3 text-sm leading-relaxed text-ink-muted [&_a]:font-semibold [&_a]:text-navy-700 [&_a]:underline [&_a]:decoration-line [&_a]:underline-offset-2 [&_a]:transition-colors [&_a]:duration-200 hover:[&_a]:text-navy-900 [&_li]:ml-5 [&_ol]:list-decimal [&_ol]:space-y-1.5 [&_strong]:font-semibold [&_strong]:text-ink [&_ul]:list-disc [&_ul]:space-y-1.5">
        {{ $slot }}
    </div>
</section>
