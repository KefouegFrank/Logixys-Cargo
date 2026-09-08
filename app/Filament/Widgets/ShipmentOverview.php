<?php

namespace App\Filament\Widgets;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ShipmentOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected static ?int $sort = 1;

    /** Statuses that mean a shipment is still on its way. */
    private const IN_FLIGHT = [
        ShipmentStatus::Pending,
        ShipmentStatus::PickedUp,
        ShipmentStatus::InTransit,
        ShipmentStatus::AtCustoms,
        ShipmentStatus::OutForDelivery,
    ];

    /** Statuses where a shipment stopped moving and needs someone to act on it. */
    private const BLOCKED = [ShipmentStatus::OnHold, ShipmentStatus::Returned];

    protected function getStats(): array
    {
        $monthStart = Carbon::now()->startOfMonth();

        $inFlight = Shipment::query()->whereIn('status', self::IN_FLIGHT)->count();
        $blocked = Shipment::query()->whereIn('status', self::BLOCKED)->count();
        $overdue = Shipment::overdue()->count();

        $deliveredThisMonth = Shipment::query()
            ->where('status', ShipmentStatus::Delivered)
            ->where('delivered_at', '>=', $monthStart)
            ->count();

        return [
            Stat::make('Expéditions en cours', $inFlight)
                ->description('En mouvement en ce moment')
                ->chart($this->weeklyCreatedTrend())
                ->color('info'),

            Stat::make('En retard', $overdue)
                ->description($overdue > 0 ? 'Livraison prévue dépassée' : 'Aucun retard')
                ->descriptionColor($overdue > 0 ? 'danger' : 'success')
                ->color($overdue > 0 ? 'danger' : 'gray'),

            Stat::make('Bloquées', $blocked)
                ->description($blocked > 0 ? 'En attente ou retournées' : 'Aucune')
                ->descriptionColor($blocked > 0 ? 'warning' : 'success')
                ->color($blocked > 0 ? 'warning' : 'gray'),

            Stat::make('Livrées ce mois', $deliveredThisMonth)
                ->description($monthStart->translatedFormat('F Y'))
                ->color('success'),
        ];
    }

    /** @return array<int, int> Shipments created per day over the last week, for the sparkline. */
    private function weeklyCreatedTrend(): array
    {
        $counts = Shipment::query()
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->get(['created_at'])
            ->countBy(fn (Shipment $shipment) => $shipment->created_at->toDateString());

        return collect(range(6, 0))
            ->map(fn (int $daysAgo) => $counts->get(Carbon::now()->subDays($daysAgo)->toDateString(), 0))
            ->all();
    }
}
