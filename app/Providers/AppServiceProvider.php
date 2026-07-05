<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Models\SerologyTest;
use App\Observers\SerologyTestObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SerologyTest::observe(SerologyTestObserver::class);
    }
}
