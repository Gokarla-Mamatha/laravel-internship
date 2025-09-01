<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Carbon\CarbonInterval;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Settings;

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
        //Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
        Passport::loadKeysFrom(base_path('secrets/oauth'));

        Passport::tokensExpireIn(CarbonInterval::days(15));
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(6));

        
    }
}
