<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Admin\GetAnalyticsHeaderData;
use Illuminate\Database\Eloquent\Relations\Relation;
use Common\Admin\Analytics\Actions\GetAnalyticsHeaderDataAction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // TEMP: disable deprecated warnings on php 7.4 until upgrade to laravel 6
        if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
            error_reporting(E_ALL ^ E_DEPRECATED);
        }

        //make sure tags relation is loaded properly
        Relation::morphMap([
            'Common\Files\FileEntry' => 'App\FileEntry',
            'App\User' => 'Common\Files\FileEntryUser',
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            GetAnalyticsHeaderDataAction::class,
            GetAnalyticsHeaderData::class
        );
    }
}
