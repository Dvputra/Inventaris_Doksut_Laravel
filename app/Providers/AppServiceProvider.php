<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.tailwind');

        \Carbon\Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'id', 'Indonesian');

        \Illuminate\Support\Facades\Blade::directive('formatJumlah', function ($expression) {
            return "<?php 
                \$__val = (float) ($expression); 
                echo floor(\$__val) == \$__val ? number_format(\$__val, 0, ',', '.') : rtrim(rtrim(number_format(\$__val, 2, ',', '.'), '0'), ','); 
            ?>";
        });
    }
}
