<?php

namespace App\Providers;

use App\Models\SerologyTest;
use App\Observers\SerologyTestObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Application services can be bound here when needed.
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
    {
        $this->configureApplicationSecurity();
        $this->configureModelBehaviour();
        $this->registerObservers();
    }

   /**
     * Register model observers used by the application.
     */
    private function registerObservers(): void
    {
        SerologyTest::observe(SerologyTestObserver::class);
    }

    /**
     * Configure model behaviour for safer development.
     */
    private function configureModelBehaviour(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
    }

    /**
     * Force HTTPS URLs when the application is running in production.
     */
    private function configureApplicationSecurity(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}

