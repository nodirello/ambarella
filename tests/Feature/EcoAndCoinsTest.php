<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EcoTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcoAndCoinsTest extends TestCase
{
    use RefreshDatabase;

    public function test_completing_eco_task_grants_coins(): void
    {
        $user = User::factory()->create();
        $task = EcoTask::factory()->create(['reward' => 25, 'requires_proof' => false]);

        $this->actingAs($user)
            ->post("/eco/tasks/{$task->id}/complete", ['proof_text' => 'Bajardim!'])
            ->assertRedirect();

        $this->assertSame(25, $user->fresh()->greencoin_balance);
        $this->assertDatabaseHas('eco_task_completions', [
            'eco_task_id' => $task->id,
            'user_id' => $user->id,
            'status' => 'approved',
        ]);
    }

    public function test_task_cannot_be_completed_twice_per_day(): void
    {
        $user = User::factory()->create();
        $task = EcoTask::factory()->create(['requires_proof' => true]);

        $this->actingAs($user)->post("/eco/tasks/{$task->id}/complete", ['proof_text' => 'Birinchi']);
        $this->actingAs($user)->post("/eco/tasks/{$task->id}/complete", ['proof_text' => 'Ikkinchi'])
            ->assertSessionHas('errors');
    }

    public function test_transfer_between_users(): void
    {
        $from = User::factory()->create(['greencoin_balance' => 100]);
        $to = User::factory()->create();

        $this->actingAs($from)->post('/greencoin/transfer', [
            'phone_or_email' => $to->email,
            'amount' => 40,
        ])->assertRedirect();

        $this->assertSame(60, $from->fresh()->greencoin_balance);
        $this->assertSame(40, $to->fresh()->greencoin_balance);
    }
}
