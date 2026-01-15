<?php

declare(strict_types=1);

namespace Wallet\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class CoreMakeLivewire extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'wallet:make-livewire {name}';

    /**
     * The console command description.
     */
    protected $description = 'Create a Wallet Livewire component';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $files): int
    {
        $name = Str::studly($this->argument('name'));
        $kebab = Str::kebab($name);

        $classPath = base_path("packages/Wallet/Core/src/Http/Livewire/Components/{$name}.php");
        $viewPath  = base_path("packages/Wallet/Core/resources/views/livewire/{$kebab}.blade.php");

        if ($files->exists($classPath)) {
            $this->error("Component {$name} already exists.");
            return self::FAILURE;
        }

        // Ensure directories exist
        $files->ensureDirectoryExists(dirname($classPath));
        $files->ensureDirectoryExists(dirname($viewPath));

        // PHP class
        $files->put($classPath, $this->livewireClassStub($name, $kebab));

        // Blade view
        $files->put($viewPath, $this->livewireViewStub($name));

        $this->info("✔ Vendly Admin Livewire component [{$name}] created.");
        $this->warn("⚠ Remember to register it in LivewireRegistrar.");

        return self::SUCCESS;
    }

    protected function livewireClassStub(string $name, string $kebab): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Livewire\Component;

class {$name} extends Component
{
    public function render()
    {
        return view('core::livewire.{$kebab}')
            ->layout('core::layouts.app');
    }
}
PHP;
    }

    protected function livewireViewStub(string $name): string
    {
        return <<<BLADE
<div>
    <!-- {$name} component -->
</div>
BLADE;
    }
}
