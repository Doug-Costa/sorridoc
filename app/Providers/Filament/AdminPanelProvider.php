<?php

namespace App\Providers\Filament;

use App\Filament\Portal\CompanyResource\CompanyResource;
use App\Filament\Portal\WorkerDocumentResource\WorkerDocumentResource;
use App\Filament\Portal\WorkerResource\WorkerResource;
use App\Filament\Resources\ApprovalFlowResource;
use App\Filament\Resources\ApprovalResource;
use App\Filament\Resources\UserResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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
            ->globalSearch(false)
            ->brandName('SorriDoc')
            ->brandLogo(null)
            ->favicon(null)
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('PRINCIPAL'),
                NavigationGroup::make()
                    ->label('PORTAL SORRIMED'),
                NavigationGroup::make()
                    ->label('CONFIGURAÇÕES'),
            ])
            ->renderHook(
                'panels::body.end',
                fn (): string => '<style>.fi-footer { display: none !important; }</style>',
            )
            ->resources([
                ApprovalResource::class,
                ApprovalFlowResource::class,
                UserResource::class,
                CompanyResource::class,
                WorkerResource::class,
                WorkerDocumentResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
