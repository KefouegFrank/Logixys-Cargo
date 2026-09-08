@props(['status'])

@if ($status->isException())
    @php
        $classes = $status->exceptionSeverity() === 'danger'
            ? 'border-red-300 bg-red-50 text-red-800'
            : 'border-amber-300 bg-amber-50 text-amber-800';
    @endphp
    <div role="status" class="rounded-md border {{ $classes }} px-4 py-3 font-medium">
        {{ $status->label() }}
    </div>
@else
    @php $currentStep = $status->step(); @endphp
    {{-- Stacked on a phone: four labels side by side wrap into each other below ~640px. --}}
    <ol class="space-y-3 sm:flex sm:items-start sm:space-y-0" aria-label="{{ __('tracking.progress_label') }}">
        @foreach (\App\Enums\ShipmentStatus::stepMilestones() as $step => $milestoneStatus)
            <li @class(['flex items-center sm:items-start', 'sm:flex-1' => ! $loop->last])>
                <div class="flex items-center gap-3 sm:flex-col sm:gap-0">
                    <span
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold
                            {{ $step <= $currentStep ? 'bg-ink text-white' : 'border-2 border-line text-ink-subtle' }}"
                    >
                        {{ $step }}
                    </span>
                    <span
                        class="text-sm sm:mt-2 sm:max-w-[7rem] sm:text-center sm:text-xs
                            {{ $step <= $currentStep ? 'font-medium text-ink' : 'text-ink-subtle' }}"
                    >
                        {{ $milestoneStatus->label() }}
                    </span>
                </div>
                @unless ($loop->last)
                    <div class="mx-2 mt-4 hidden h-0.5 flex-1 sm:block {{ $step < $currentStep ? 'bg-ink' : 'bg-line' }}"></div>
                @endunless
            </li>
        @endforeach
    </ol>
@endif
