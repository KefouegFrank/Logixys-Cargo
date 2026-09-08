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
    {{-- Horizontal at every width. The circles, labels and connectors all shrink together
         so four steps still fit across a 320px screen. --}}
    <ol class="flex items-start" aria-label="{{ __('tracking.progress_label') }}">
        @foreach (\App\Enums\ShipmentStatus::stepMilestones() as $step => $milestoneStatus)
            <li @class(['flex items-center', 'flex-1' => ! $loop->last])>
                <div class="flex flex-col items-center">
                    <span
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold sm:h-8 sm:w-8 sm:text-sm
                            {{ $step <= $currentStep ? 'bg-ink text-white' : 'border-2 border-line text-ink-subtle' }}"
                    >
                        {{ $step }}
                    </span>
                    <span
                        class="mt-1.5 max-w-[4.25rem] text-center text-[11px] leading-tight sm:mt-2 sm:max-w-[7rem] sm:text-xs
                            {{ $step <= $currentStep ? 'font-medium text-ink' : 'text-ink-subtle' }}"
                    >
                        {{ $milestoneStatus->label() }}
                    </span>
                </div>
                @unless ($loop->last)
                    <div class="mx-1.5 mt-3.5 h-0.5 flex-1 sm:mx-2 sm:mt-4 {{ $step < $currentStep ? 'bg-ink' : 'bg-line' }}"></div>
                @endunless
            </li>
        @endforeach
    </ol>
@endif
