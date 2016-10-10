<?php

namespace Mixdinternet\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Mixdinternet\Core\Commands\ImageClear;
use Mixdinternet\Core\Commands\SessionClear;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Http\Kernel;
use Mixdinternet\Core\Http\Middleware\ForceUrlMiddleware;

class CoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->setRoutes();

        $this->setMiddlewares();

        $this->setCommands();

        $this->publish();
    }

    public function register()
    {
        config(include __DIR__ . '/../config/app.php');

        if (request()->ip() == env('APP_DEBUG_IP')) {
            config(['app.debug' => true]);
        }

        $this->loadConfigs();
    }

    protected function setRoutes()
    {
        if (!$this->app->routesAreCached()) {
            $this->app->router->group(['namespace' => 'Mixdinternet\Core\Http\Controllers'],
                function () {
                    require __DIR__ . '/../routes/web.php';
                });
        }
    }

    protected function setMiddlewares()
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(ForceUrlMiddleware::class);
    }

    protected function setCommands()
    {
        $this->commands([ImageClear::class]);
        $this->commands([SessionClear::class]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('session:clear')->dailyAt('03:00');
        });
    }

    protected function publish()
    {
        $this->publishes([
            __DIR__ . '/../config/deploy.php' => base_path('config/deploy.php'),
            __DIR__ . '/../.hooks' => base_path('.hooks')
        ], 'install');
    }

    protected function loadConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/deploy.php', 'deploy');
    }
}
