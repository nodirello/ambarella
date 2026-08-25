<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_jobs_api_returns_json(): void
    {
        Job::factory()->count(2)->create();

        $this->getJson('/api/v1/jobs')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_token_issue_flow(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/token', [
            'email' => $user->email,
            'password' => 'Password123',
        ])->assertOk();

        $this->assertArrayHasKey('token', $response->json());
    }

    public function test_authenticated_endpoints_require_token(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_token_authentication_works(): void
    {
        $user = User::factory()->create();
        $plain = 'test-token-123';
        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'test',
            'token_hash' => ApiToken::hashToken($plain),
            'abilities' => ['*'],
        ]);

        $this->withToken($plain)
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id);
    }
}
