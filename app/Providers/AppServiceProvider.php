<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use App\Services\Interfaces\IAuthService;
use App\Services\Classes\AuthService;
use App\Services\Interfaces\IUserService;
use App\Services\Classes\UserService;
use App\Services\Classes\WalletService;
use App\Services\Interfaces\IWalletService;
use App\Services\Interfaces\ITransactionService;
use App\Services\Classes\TransactionService;
use App\Services\Interfaces\IExchangeRateService;
use App\Services\Classes\ExchangeRateService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(IWalletService::class, WalletService::class);
        $this->app->bind(ITransactionService::class, TransactionService::class);
        $this->app->bind(IExchangeRateService::class, ExchangeRateService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //automatically run seeders if env is local
        if (app()->environment('local')) {
            Artisan::call('migrate --seed');
        }
    }
}
