<x-filament-panels::page>
    <form wire:submit="save" class="grid gap-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit">
                Enregistrer
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
