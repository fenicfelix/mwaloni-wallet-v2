<?php

namespace Wallet\Core\Http\Enums;

enum TransactionStatus: string
{
    case REQUESTED = 'requested'; // Initial state waiting for approval
    case CANCELLED = 'cancelled'; // User has cancelled the transaction
    case PENDING = 'pending'; // Transaction is pending
    case PROCESSING = 'processing'; // Transaction is being processed
    case SUBMITTED = 'submitted'; // Transaction has been submitted
    case SUCCESS = 'success'; // Transaction was successful
    case FAILED = 'failed'; // Transaction has failed
    case COMPLETED = 'completed'; // Transaction is completed - Moves failed transactions from statistics
    case REVERSING = 'reversing'; // Transaction is being reversed
    case REVERSED = 'reversed'; // Transaction has been reversed
    case QUERYING_STATUS = 'querying_status'; // Transaction status is being queried
    case REVERSING_FAILED = 'reversing_failed'; // Transaction reversal has failed

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
            self::REQUESTED->value => 'Requested',
            self::CANCELLED->value => 'Cancelled',
            self::PENDING->value => 'Pending',
            self::PROCESSING->value => 'Processing',
            self::SUBMITTED->value => 'Submitted',
            self::SUCCESS->value => 'Success',
            self::FAILED->value => 'Failed',
            self::COMPLETED->value => 'Completed',
            self::REVERSING->value => 'Reversing',
            self::REVERSED->value => 'Reversed',
            self::QUERYING_STATUS->value => 'Querying Status',
            self::REVERSING_FAILED->value => 'Reversing Failed',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public static function fromValue(string $value): self
    {
        return match ($value) {
            'requested' => self::REQUESTED,
            'cancelled' => self::CANCELLED,
            'pending' => self::PENDING,
            'processing' => self::PROCESSING,
            'submitted' => self::SUBMITTED,
            'success' => self::SUCCESS,
            'failed' => self::FAILED,
            'completed' => self::COMPLETED,
            'reversing' => self::REVERSING,
            'reversed' => self::REVERSED,
            'querying_status' => self::QUERYING_STATUS,
            'reversing_failed' => self::REVERSING_FAILED,
            default => throw new \InvalidArgumentException("Invalid transaction status: $value"),
        };
    }

    public function backgroundColor(): string
    {
        return match ($this) {
            self::REQUESTED => 'info-lt',
            self::CANCELLED => 'danger-lt',
            self::PENDING => 'secondary-lt',
            self::PROCESSING => 'primary-lt',
            self::SUBMITTED => 'primary-lt',
            self::SUCCESS => 'success-lt',
            self::FAILED => 'danger-lt',
            self::COMPLETED => 'dark-lt',
            self::REVERSING => 'primary-lt',
            self::REVERSED => 'dark-lt',
            self::QUERYING_STATUS => 'secondary-lt',
            self::REVERSING_FAILED => 'danger-lt',
        };
    }
}
