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
    <ol class="flex items-start" aria-label="{{ __('tracking.progress_label') }}">
        @foreach (\App\Enums\ShipmentStatus::stepMilestones() as $step => $milestoneStatus)
            <li class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                <div class="flex flex-col items-center">
                    <span
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold
                            {{ $step <= $currentStep ? 'bg-ink text-white' : 'border-2 border-line text-ink-subtle' }}"
                    >
                        {{ $step }}
                    </span>
                    <span
                        class="mt-2 max-w-[6rem] text-center text-xs
                            {{ $step <= $currentStep ? 'font-medium text-ink' : 'text-ink-subtle' }}"
                    >
                        {{ $milestoneStatus->label() }}
                    </span>
                </div>
                @unless ($loop->last)
                    <div class="mx-2 mt-4 h-0.5 flex-1 {{ $step < $currentStep ? 'bg-ink' : 'bg-line' }}"></div>
                @endunless
            </li>
        @endforeach
    </ol>
@endif
