## Installation

Run the following commands to install dependencies and publish necessary assets

```bash
composer require wallet/core:@dev -W  # Wallet Core Package
php artisan vendor:publish --tag="livewire-tables-config" # Publish datatable
php artisan horizon:install # Install horizon

# Publish log-viewer assets
php artisan vendor:publish --tag="log-viewer-config"
php artisan log-viewer:publish
```
