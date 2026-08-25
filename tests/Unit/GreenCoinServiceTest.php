<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\CoinTransactionType;
use App\Models\User;
use App\Services\GreenCoinService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GreenCoinServiceTest extends TestCase
{
    use RefreshDatabase;

    private GreenCoinService $coins;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coins = app(GreenCoinService::class);
    }

    public function test_credit_updates_balance_and_ledger(): void
    {
        $user = User::factory()->create();

        $tx = $this->coins->credit($user, 100, 'Test mukofot');

        $this->assertDatabaseHas('greencoin_transactions', [
            'user_id' => $user->id,
            'amount' => 100,
            'type' => CoinTransactionType::Earned->value,
            'balance_after' => 100,
        ]);
        $this->assertSame(100, $user->fresh()->greencoin_balance);
        $this->assertTrue($tx->isCredit());
    }

    public function test_debit_cannot_go_below_zero(): void
    {
        $user = User::factory()->create(['greencoin_balance' => 10]);

        $this->coins->debit($user, 5, 'Xarajat');

        $this->assertSame(5, $user->fresh()->greencoin_balance);

        $this->expectException(\RuntimeException::class);
        $this->coins->debit($user, 50, 'Imkonsiz xarajat');
    }

    public function test_transfer_moves_coins_between_users(): void
    {
        $from = User::factory()->create(['greencoin_balance' => 100]);
        $to = User::factory()->create(['greencoin_balance' => 0]);

        $this->coins->transfer($from, $to, 60);

        $this->assertSame(40, $from->fresh()->greencoin_balance);
        $this->assertSame(60, $to->fresh()->greencoin_balance);
        $this->assertDatabaseCount('greencoin_transactions', 2);
    }

    public function test_transfer_fails_without_funds(): void
    {
        $from = User::factory()->create(['greencoin_balance' => 10]);
        $to = User::factory()->create();

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->coins->transfer($from, $to, 50);
    }
}
