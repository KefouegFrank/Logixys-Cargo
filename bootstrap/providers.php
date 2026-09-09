<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\QueueServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    QueueServiceProvider::class,
];
