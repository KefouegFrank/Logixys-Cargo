<?php

namespace App\Filament\Widgets;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Filament\Widgets\ChartWidget;

class ShipmentsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Répartition par statut';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = Shipment::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statuses = collect(ShipmentStatus::cases())
            ->filter(fn (ShipmentStatus $status) => ($counts[$status->value] ?? 0) > 0);

        return [
            'datasets' => [[
                'label' => 'Expéditions',
                'data' => $statuses->map(fn (ShipmentStatus $s) => $counts[$s->value])->values()->all(),
                'backgroundColor' => $statuses->map(fn (ShipmentStatus $s) => self::SWATCHES[$s->value])->values()->all(),
                'borderWidth' => 0,
            ]],
            'labels' => $statuses->map(fn (ShipmentStatus $s) => $s->label())->values()->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'right']],
            'cutout' => '62%',
        ];
    }

    /*
     * Navy ramp for the happy path so the wheel reads as one family, with the
     * public site's success/warning/danger hues reserved for the exceptions.
     */
    private const SWATCHES = [
        'PENDING' => '#a8bdd6',
        'PICKED_UP' => '#849ebd',
        'IN_TRANSIT' => '#466285',
        'AT_CUSTOMS' => '#f9d52a',
        'OUT_FOR_DELIVERY' => '#2e496a',
        'DELIVERED' => '#065f46',
        'ON_HOLD' => '#92400e',
        'RETURNED' => '#9e7d00',
        'CANCELLED' => '#991b1b',
    ];
}
