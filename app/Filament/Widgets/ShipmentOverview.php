<?php

namespace App\Filament\Widgets;

use App\Enums\ShipmentStatus;
use App\Models\ContactMessage;
use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ShipmentOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $monthStart = Carbon::now()->startOfMonth();

        $inFlight = Shipment::query()
            ->whereIn('status', [
                ShipmentStatus::Pending,
                ShipmentStatus::PickedUp,
                ShipmentStatus::InTransit,
                ShipmentStatus::AtCustoms,
                ShipmentStatus::OutForDelivery,
            ])
            ->count();

        $onHold = Shipment::query()
            ->whereIn('status', [ShipmentStatus::OnHold, ShipmentStatus::Returned])
            ->count();

        $deliveredThisMonth = Shipment::query()
            ->where('status', ShipmentStatus::Delivered)
            ->where('delivered_at', '>=', $monthStart)
            ->count();

        $revenue = Shipment::query()
            ->where('created_at', '>=', $monthStart)
            ->sum('total_ttc');

        $unhandled = ContactMessage::query()->where('is_handled', false)->count();

        return [
            Stat::make('Expéditions en cours', $inFlight)
                ->description($onHold > 0 ? "{$onHold} en attente ou retournées" : 'Aucun incident')
                ->descriptionColor($onHold > 0 ? 'warning' : 'success')
                ->chart($this->weeklyCreatedTrend())
                ->color('info'),

            Stat::make('Livrées ce mois', $deliveredThisMonth)
                ->description($monthStart->translatedFormat('F Y'))
                ->color('success'),

            Stat::make('Chiffre d\'affaires du mois', number_format((float) $revenue, 2, ',', ' ').' €')
                ->description('TTC, sur les expéditions créées')
                ->color('primary'),

            Stat::make('Messages non traités', $unhandled)
                ->description($unhandled > 0 ? 'À relancer' : 'Boîte à jour')
                ->descriptionColor($unhandled > 0 ? 'warning' : 'success')
                ->color($unhandled > 0 ? 'warning' : 'gray'),
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
