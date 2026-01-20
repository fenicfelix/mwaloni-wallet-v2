## Installation

- Run the following commands to install dependencies and publish necessary assets

```bash
composer require wallet/core:@dev -W  # Wallet Core Package

# Publish datatable
php artisan vendor:publish --tag="livewire-tables-config"

# Install horizon
php artisan horizon:install
# Publish log-viewer assets
php artisan vendor:publish --tag="log-viewer-config"
php artisan log-viewer:publish
```
