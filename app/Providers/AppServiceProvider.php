<?php

namespace App\Providers;

use App\Services\ProvidesApiContract;
use App\Services\ProvideApiContractFromConfigService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProvidesApiContract::class, ProvideApiContractFromConfigService::class);
    }

    public function boot(): void
    {
    }
}
