<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Enums;

enum PermissionType: string
{
    case ALL = 'all';
    case CUSTOM = 'custom';

    public static function values(): array
    {
        return array_map(
            fn(self $type) => $type->value,
            self::cases()
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::ALL => 'All Permissions',
            self::CUSTOM => 'Custom Permissions',
        };
    }

    public static function fromValue(string $value): self
    {
        return match ($value) {
            'all' => self::ALL,
            'custom' => self::CUSTOM,
            default => throw new \InvalidArgumentException("Invalid permission type: $value"),
        };
    }
}
