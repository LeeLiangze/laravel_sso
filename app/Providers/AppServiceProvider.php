<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        DB::listen(function($query) {
//            $tmp = str_replace('?', '"'.'%s'.'"', $query->sql);
//            $tmp = vsprintf($tmp, $query->bindings);
//            $tmp = str_replace("\\","",$tmp);
//            Log::info($tmp."\n\n\t");
//        });
        //
    }
}