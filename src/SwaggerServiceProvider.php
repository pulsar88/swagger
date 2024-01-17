<?php

namespace Fillincode\Swagger;

use Fillincode\Swagger\Console\DestroyDataCommand;
use Fillincode\Swagger\Console\GenerateCommand;
use Fillincode\Swagger\Console\ParseCommand;
use Illuminate\Support\ServiceProvider;

class SwaggerServiceProvider extends ServiceProvider
{
    /**
     * Make driver
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/swagger.php' => config_path('swagger.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                DestroyDataCommand::class,
                GenerateCommand::class,
                ParseCommand::class,
            ]);
        }
    }
}