<?php

declare(strict_types=1);

namespace Wallet\Core\Providers;

use Akika\LaravelStanbic\Events\Pain00200103ReportReceived;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Wallet\Core\Http\Controllers\Middleware\VerifyMwaloniHeaders;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Opcodes\LogViewer\Facades\LogViewer;
use Wallet\Core\Console\Commands\CoreMakeLivewire;
use Wallet\Core\Console\Commands\PopulateTransactionMetricTableCommand;
use Wallet\Core\Console\Commands\TestApi;
use Wallet\Core\Http\Livewire\LivewireRegistrar;
use Wallet\Core\Listeners\StanbicStatusReportEventListener;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Wallet\Core\Console\Commands\ClearLogs;
use Wallet\Core\Console\Commands\FetchAccountBalanceCommand;
use Wallet\Core\Console\Commands\ProcessPendingPayments;

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
                ProcessPendingPayments::class,
                ClearLogs::class,
                FetchAccountBalanceCommand::class,
                TestApi::class,
            ]);
        }

        Event::listen(
            Pain00200103ReportReceived::class,
            StanbicStatusReportEventListener::class
        );

        // Register Livewire components
        LivewireRegistrar::register();

        // Load the web routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/core.php');

        // Define a custom rate limiter for the api-key
        RateLimiter::for('mwaloni-api', function (Request $request) {
            $apiKey = $request->header('x-api-key');
            return Limit::perMinute(120)
                ->by($apiKey ?: $request->ip());
        });

        Route::middleware(['api', 'throttle:mwaloni-api', VerifyMwaloniHeaders::class])
            ->prefix('api')
            ->group(__DIR__ . '/../../routes/api.php');

        app(VerifyCsrfToken::class)->except([
            '*drj-callback/*',
        ]);

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

        LogViewer::auth(function ($request) {
            return $request->user()
                && in_array($request->user()->email, [
                    'akika.digital@gmail.com',
                    'osanjo@gmail.com'
                ]);
        });
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
