<?php

namespace App\Providers;

use App\Pagination\CustomPaginator;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->alias(CustomPaginator::class, LengthAwarePaginator::class); // Eloquent uses the class instead of the contract 🤔
        $this->app->alias(CustomPaginator::class, LengthAwarePaginatorContract::class);
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::listen(function (QueryExecuted $query) {
            Log::info( "Sql executed: $query->sql", $query->bindings);
        });
    }
}
