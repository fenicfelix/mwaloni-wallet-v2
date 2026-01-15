## Installation

```bash
composer require wallet/core:@dev -W  # Wallet Core Package

# Install Akika Wallet related packages
composer require akika/laravel-mpesa-multivendor
composer require akika/laravel-ncba
composer require akika/laravel-stanbic

# Other dependacies
composer require africastalking/africastalking
composer require rappasoft/laravel-livewire-tables
# Publish datatable
php artisan vendor:publish --tag="livewire-tables-config"
```