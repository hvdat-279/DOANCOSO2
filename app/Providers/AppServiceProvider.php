<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Models\shopping_cart;
use Illuminate\Support\Facades\Auth;

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
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        view()->composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $totalQuantity = shopping_cart::where('user_id', $user->id)->sum('quantity');

                $view->with('totalQuantity', $totalQuantity);
            } else {

                $view->with('totalQuantity', 0);
            }
            // $view->with([
            //     'cart' => new cart(),
            // ]);
        });
    }
}
