<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

use App\Models\Setting;
use App\Services\Cybersource\CybersourceClient;
use App\Models\User;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // The Cybersource client takes its credentials as constructor state,
        // so it cannot be autowired from the container alone.
        $this->app->singleton(CybersourceClient::class, function () {
            return new CybersourceClient(config('cybersource'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Any authenticated admin (web) user has full access — roles/permissions
        // are not required in this app. This grants every ability (covers @can
        // gates and Spatie's permission: middleware, which uses canAny()).
        Gate::before(function ($user, string $ability) {
            return $user instanceof User ? true : null;
        });

        // Load settings into config(). Guarded, because the container boots
        // before the table exists on a fresh install or in tests.
        if (Schema::hasTable((new Setting)->getTable())) {
            foreach (Setting::all() as $setting) {
                config(['setting.' . $setting->name => $setting->value]);
            }
        }
    }
}
