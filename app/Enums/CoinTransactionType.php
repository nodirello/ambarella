<?php

declare(strict_types=1);

namespace App\Enums;

enum CoinTransactionType: string
{
    case Earned = 'earned';
    case Spent = 'spent';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';
    case Adjustment = 'adjustment';
    case Reward = 'reward';

    public function isCredit(): bool
    {
        return match ($this) {
            self::Earned, self::TransferIn, self::Reward => true,
            default => false,
        };
    }
}
