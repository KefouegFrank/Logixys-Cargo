<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LatestShipments;
use App\Filament\Widgets\ShipmentOverview;
use App\Filament\Widgets\ShipmentsByStatusChart;
use Filament\FontProviders\BunnyFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Logixys Cargo')
            ->favicon(asset('favicon.svg'))
            ->brandLogo(fn () => $this->brandLogo())
            ->darkModeBrandLogo(fn () => $this->brandLogo(isDarkMode: true))
            ->brandLogoHeight(fn () => request()->routeIs('filament.admin.auth.*') ? '3rem' : '2.25rem')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->font(config('brand.fonts.body'), provider: BunnyFontProvider::class)
            // ->font() covers one family; headings need the second one loaded by hand.
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => new HtmlString(sprintf(
                '<link href="https://fonts.bunny.net/css?family=%s:600,700" rel="stylesheet" />',
                urlencode(strtolower(config('brand.fonts.heading'))),
            )))
            ->colors([
                /*
                 * Filament reads a palette, not a base colour: Color::hex() keeps only the
                 * hue and imposes its own lightness/chroma, which turned the gold olive and
                 * the navy into a vivid azure. Hand it the ramps the public site already uses.
                 */
                'primary' => config('brand.ramps.gold'),
                'info' => config('brand.ramps.navy'),
                // Slate is the low-chroma neutral nearest the navy hue; the navy ramp itself
                // is far too saturated to carry every surface, border and muted label.
                'gray' => Color::Slate,
                // Amber is the Filament default and would read as the gold primary.
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                // Icons live on the items; Filament rejects having them on both.
                NavigationGroup::make('Exploitation'),
                NavigationGroup::make('Administration'),
            ])
            ->profile(isSimple: false)
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                ShipmentOverview::class,
                LatestShipments::class,
                ShipmentsByStatusChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Panel chrome is navy in both themes, so it always takes the light lockup;
     * only the auth card, which is white in light mode, needs the dark one.
     */
    private function brandLogo(bool $isDarkMode = false): HtmlString
    {
        $onWhite = (! $isDarkMode) && request()->routeIs('filament.admin.auth.*');

        return new HtmlString(sprintf(
            '<img src="%s" alt="%s" width="%d" height="%d" class="fi-brand-lockup">',
            asset(config('brand.lockup.'.($onWhite ? 'dark' : 'light'))),
            e(config('app.name')),
            config('brand.lockup.width'),
            config('brand.lockup.height'),
        ));
    }
}
