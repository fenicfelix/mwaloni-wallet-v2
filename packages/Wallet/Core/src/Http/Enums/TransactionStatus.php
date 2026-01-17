<?php

namespace Wallet\Core\Http\Enums;

enum TransactionStatus: string
{
    case SUBMITTED = 'submitted';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REVERSING = 'reversing';
    case PENDING = 'pending';
    case REVERSED = 'reversed';
    case QUERYING_STATUS = 'querying_status';

    public static function values(): array
    {
        return array_map(
            fn(self $type) => $type->value,
            self::cases()
        );
    }

    public static function labels(): array
    {
        return [
            self::SUBMITTED->value => 'Submitted',
            self::SUCCESS->value => 'Success',
            self::FAILED->value => 'Failed',
            self::CANCELLED->value => 'Cancelled',
            self::REVERSING->value => 'Reversing',
            self::PENDING->value => 'Pending',
            self::REVERSED->value => 'Reversed',
            self::QUERYING_STATUS->value => 'Querying Status',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public static function fromValue(string $value): self
    {
        return match ($value) {
            'submitted' => self::SUBMITTED,
            'success' => self::SUCCESS,
            'failed' => self::FAILED,
            'cancelled' => self::CANCELLED,
            'reversing' => self::REVERSING,
            'pending' => self::PENDING,
            'reversed' => self::REVERSED,
            'querying_status' => self::QUERYING_STATUS,
            default => throw new \InvalidArgumentException("Invalid transaction status: $value"),
        };
    }

    public function backgroundColor(): string
    {
        return match ($this) {
            self::PENDING => 'secondary-lt',
            self::SUBMITTED => 'primary-lt',
            self::SUCCESS => 'success-lt',
            self::FAILED => 'danger-lt',
            self::CANCELLED => 'danger-lt',
            self::REVERSING => 'primary-lt',
            self::REVERSED => 'dark-lt',
            self::QUERYING_STATUS => 'secondary-lt',
        };
    }
}
