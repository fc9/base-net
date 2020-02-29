<?php

namespace Fc9\Net\Providers;

//use Fc9\Net\Network;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class NetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //$this->registerTranslations();
        $this->registerConfig();
        //$this->registerViews();
        //$this->registerFactories();
        //$this->registerMiddleware();
        //$this->registerBladeDirectives();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadSeeds();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton('net', function () {
            return new \Fc9\Net\Network();
        });
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/packages/net');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'net');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'net');
        }
    }

    /**
     * Register Config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([__DIR__ . '/../config/net.php' => config_path('net.php'),], 'Config');

        $this->mergeConfigFrom(__DIR__ . '/../config/net.php', 'net');
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/packages/net');

        $sourcePath = __DIR__ . '/../resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(
            array_merge(
                array_map(
                    function ($path) {
                        return $path . '/vendor/fc9/net';
                    },
                    config()->get('view.paths')),
                [$sourcePath]
            ),
            'net');
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../database/factories');
        }
    }

    public function registerMiddleware()
    {
        $router = $this->app['router'];
        //$router->aliasMiddleware('net', Network::class);
    }

    /**
     * Register additional directives of Blade.
     *
     * @return void
     */
    public function registerBladeDirectives()
    {
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    public function loadSeeds()
    {
        if ($this->app->runningInConsole()) {
            $command = Request::server('argv', null);
            if (is_array($command)) {
                $command = implode(' ', $command);
                if ($command == "artisan db:seed") {
                    Artisan::call('db:seed', ['--class' => \Fc9\Net\Database\Seeders\DatabaseSeeder::class]);
                }
            }
        }
    }
}
