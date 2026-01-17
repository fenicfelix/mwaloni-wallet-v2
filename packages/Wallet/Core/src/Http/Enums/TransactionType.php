<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Enums;

enum TransactionType: string
{
    case PAYMENTS = 'payments';
    case ACCOUNT_BALANCE = 'account_balance';
    case REVERSAL = 'reversal';
    case CASHOUT = 'cashout';
    case SERVICE_CHARGE = 'service_charge';
    case DISTRIBUTE = 'distribute';
    case WITHDRAW = 'withdraw';
    case REVENUE_TRANSFER = 'revenue_transfer';

    public static function descriptions(): array
    {
        return [
            self::PAYMENTS->value => 'Used to send money from business to customer e.g. refunds.',
            self::ACCOUNT_BALANCE->value => 'Used to check the balance in a paybill/buy goods account (includes utility, MMF, Merchant, Charges paid account).',
            self::REVERSAL->value => 'Reversal for an erroneous C2B transaction.',
            self::CASHOUT->value => 'Cashing out generated revenue.',
            self::SERVICE_CHARGE->value => 'Monthly service charges',
            self::DISTRIBUTE->value => 'Distribute from client account to service account',
            self::WITHDRAW->value => 'Withdraw cash from a service',
            self::REVENUE_TRANSFER->value => 'Revenue transfer from a service to an account',
        ];
    }

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
            self::PAYMENTS->value => 'Payments',
            self::ACCOUNT_BALANCE->value => 'Account Balance',
            self::REVERSAL->value => 'Reversal',
            self::CASHOUT->value => 'Cashout',
            self::SERVICE_CHARGE->value => 'Service Charge',
            self::DISTRIBUTE->value => 'Distribute',
            self::WITHDRAW->value => 'Withdraw',
            self::REVENUE_TRANSFER->value => 'Revenue Transfer',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public static function fromValue(string $value): self
    {
        return match ($value) {
            'payments' => self::PAYMENTS,
            'account_balance' => self::ACCOUNT_BALANCE,
            'reversal' => self::REVERSAL,
            'cashout' => self::CASHOUT,
            'service_charge' => self::SERVICE_CHARGE,
            'distribute' => self::DISTRIBUTE,
            'withdraw' => self::WITHDRAW,
            'revenue_transfer' => self::REVENUE_TRANSFER,
            default => throw new \InvalidArgumentException("Invalid permission type: $value"),
        };
    }
}
