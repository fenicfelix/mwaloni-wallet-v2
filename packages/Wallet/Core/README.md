## Installation

Run the following commands to install dependencies and publish necessary assets

```bash
composer require wallet/core:@dev -W  # Wallet Core Package
php artisan vendor:publish --tag="livewire-tables-config" # Publish datatable
php artisan horizon:install # Install horizon

# Publish log-viewer assets
php artisan vendor:publish --tag="log-viewer-config"
php artisan log-viewer:publish

# Generate or refresh JWT scret key
php artisan jwt:secret
```

## Custom ENV Variables

Below should be added to 

```bash
# CUSTOM
JWT_SECRET=your_jwt_secret

# SFTP Connection Settings
STANBIC_SFTP_HOST=your-sftp-host.example.com
STANBIC_SFTP_PORT=22
STANBIC_SFTP_USERNAME=your-username
STANBIC_SFTP_PRIVATE_KEY=/path/to/your/private/key

# File System Configuration
STANBIC_FILESYSTEM_DISK=stanbic_sftp          # The disk to use for SFTP operations
STANBIC_INPUT_ROOT=Inbox                      # Directory where incoming reports are read from
STANBIC_OUTPUT_ROOT=Outbox                    # Directory where outgoing files are uploaded to

# Output File Naming
STANBIC_OUTPUT_FILE_PREFIX=MY_COMPANYC2C_Pain001v3_GH_TST_  # Prefix for generated files (include environment suffix)

# Report Processing
STANBIC_REPORTS_CLEANUP_AFTER_PROCESSING=true  # Remove processed reports from SFTP server to prevent re-processing

# Backup Settings
STANBIC_REPORTS_BACKUP_ENABLED=true            # Enable local backup of processed reports
STANBIC_REPORTS_BACKUP_DISK=local              # Local disk for storing report backups
STANBIC_REPORTS_BACKUP_ROOT=stanbic/reports    # Directory path within backup disk for storing reports
```
