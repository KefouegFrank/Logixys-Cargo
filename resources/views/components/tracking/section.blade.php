{{-- Section heading with the rule under it, as on the carrier screens this replaces. --}}
@props(['heading'])

<section>
    <h2 class="border-b border-line pb-2 font-heading text-base font-bold text-ink sm:text-lg">{{ $heading }}</h2>

    <div class="mt-5">{{ $slot }}</div>
</section>
