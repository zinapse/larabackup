<?php

namespace Zinapse\LaraBackup;

use Illuminate\Support\ServiceProvider;
use Zinapse\LaraBackup\Commands\LaravelBackupCommand;

class LaraBackupServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Load the artisan command
        if ($this->app->runningInConsole())
        {
            $this->commands([
                LaravelBackupCommand::class
            ]);
        }
    }
}