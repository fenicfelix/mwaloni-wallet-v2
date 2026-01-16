<?php

declare(strict_types=1);

namespace Wallet\Core\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Wallet\Core\Console\Commands\CoreMakeLivewire;
use Wallet\Core\Http\Livewire\LivewireRegistrar;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {}

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
            ]);
        }

        // Register Livewire components
        LivewireRegistrar::register();

        // Load the web routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/core.php');

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
