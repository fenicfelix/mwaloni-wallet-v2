<?php

declare(strict_types=1);

namespace Wallet\Core\Providers;

use Wallet\Core\Http\Controllers\Middleware\VerifyMwaloniHeaders;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Wallet\Core\Console\Commands\CoreMakeLivewire;
use Wallet\Core\Console\Commands\PopulateTransactionMetricTableCommand;
use Wallet\Core\Console\Commands\TestApi;
use Wallet\Core\Http\Livewire\LivewireRegistrar;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/../Helpers/helpers.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the admin console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CoreMakeLivewire::class,
                PopulateTransactionMetricTableCommand::class,
                TestApi::class,
            ]);
        }

        // Register Livewire components
        LivewireRegistrar::register();

        // Load the web routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/core.php');

        Route::middleware(['api', VerifyMwaloniHeaders::class])
            ->prefix('api')
            ->group(__DIR__ . '/../../routes/api.php');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Load the admin views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'core');


        $this->registerLivewireComponents(
            base_path('packages/Wallet/Core/src/Http/Livewire/Datatables'),
            'Wallet\\Core\\Http\\Livewire\\Datatables',
            'core-datatables'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../Acl/permissions.php',
            'core.acl.permissions'
        );

        // Register views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'wallet');

        // (Optional but recommended) auto-discover blade components
        Blade::componentNamespace('Wallet\\Core\\View\\Components', 'wallet');
    }

    protected function registerLivewireComponents(
        string $path,
        string $namespace,
        string $prefix
    ): void {
        if (!is_dir($path)) {
            return;
        }

        foreach (File::allFiles($path) as $file) {
            $class = $namespace . '\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                $file->getRelativePathname()
            );

            if (!class_exists($class)) {
                continue;
            }

            $componentName = $prefix . '.' . str(
                class_basename($class)
            )->kebab();

            Livewire::component($componentName, $class);
        }
    }
}
