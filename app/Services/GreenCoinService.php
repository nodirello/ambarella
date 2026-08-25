<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CoinTransactionType;
use App\Models\GreenCoinTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Immutable double-entry-ish ledger for GreenCoin.
 * Every movement writes a transaction row with the resulting balance,
 * and the balance itself is updated inside a row-locked transaction.
 */
class GreenCoinService
{
    public function credit(User $user, int $amount, string $description, CoinTransactionType $type = CoinTransactionType::Earned, ?Model $reference = null): GreenCoinTransaction
    {
        abort_if($amount <= 0, 422, 'Mukofot miqdori noto‘g‘ri.');

        return $this->move($user, $amount, $description, $type, $reference);
    }

    public function debit(User $user, int $amount, string $description, CoinTransactionType $type = CoinTransactionType::Spent, ?Model $reference = null): GreenCoinTransaction
    {
        abort_if($amount <= 0, 422, 'Miqdor noto‘g‘ri.');

        return $this->move($user, -$amount, $description, $type, $reference);
    }

    public function transfer(User $from, User $to, int $amount): void
    {
        abort_if($amount <= 0, 422, 'Transfer miqdori noto‘g‘ri.');
        abort_if($from->is($to), 422, 'O‘zingizga transfer qilib bo‘lmaydi.');

        DB::transaction(function () use ($from, $to, $amount): void {
            $lockedFrom = User::whereKey($from->id)->lockForUpdate()->firstOrFail();
            abort_if($lockedFrom->greencoin_balance < $amount, 422, 'Balans yetarli emas.');

            $this->debit(
                $lockedFrom,
                $amount,
                'Transfer → '.$to->name,
                CoinTransactionType::TransferOut
            );
            $this->credit(
                $to,
                $amount,
                'Transfer ← '.$lockedFrom->name,
                CoinTransactionType::TransferIn
            );
        });
    }

    public function adjust(User $user, int $delta, string $reason): GreenCoinTransaction
    {
        abort_if($delta === 0, 422, 'O‘zgarish 0 bo‘lishi mumkin emas.');

        return $this->move(
            $user,
            $delta,
            $reason,
            $delta > 0 ? CoinTransactionType::Adjustment : CoinTransactionType::Adjustment
        );
    }

    private function move(User $user, int $amount, string $description, CoinTransactionType $type, ?Model $reference = null): GreenCoinTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $type, $reference): GreenCoinTransaction {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $newBalance = $locked->greencoin_balance + $amount;
            if ($newBalance < 0) {
                throw new RuntimeException('Balans yetarli emas.');
            }

            $locked->update(['greencoin_balance' => $newBalance]);

            return GreenCoinTransaction::create([
                'user_id' => $locked->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'reference_type' => $reference ? $reference::class : null,
                'reference_id' => $reference?->getKey(),
                'balance_after' => $newBalance,
            ]);
        });
    }
}
