<?php

namespace App\Providers\Filament;

use Filament\FontProviders\BunnyFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
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
            ->favicon(asset('favicon.svg'))
            ->brandLogo(fn () => $this->logoHtml('logo-mark.svg', '#0D1B2A'))
            ->darkModeBrandLogo(fn () => $this->logoHtml('logo-mark-light.svg', '#FFFFFF'))
            ->brandLogoHeight('2rem')
            ->font('Open Sans', provider: BunnyFontProvider::class)
            ->colors([
                'primary' => Color::hex('#D4AF37'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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

    private function logoHtml(string $svgFile, string $textColor): HtmlString
    {
        $svg = file_get_contents(resource_path("images/{$svgFile}"));

        return new HtmlString(
            '<span style="display: flex; align-items: center; gap: 0.5rem;">'
            .'<span style="width: 1.75rem; height: 1.75rem; display: inline-flex;">'.$svg.'</span>'
            // Reuses the panel's own font (Open Sans, loaded via ->font() below) rather
            // than pulling in Montserrat for one small label.
            .'<span style="font-weight: 700; font-size: 1.05rem; color: '.$textColor.';">Logixys Cargo</span>'
            .'</span>'
        );
    }
}
