<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_jobs_are_listed(): void
    {
        Job::factory()->count(3)->create();
        Job::factory()->inactive()->create();

        $this->get('/jobs')
            ->assertOk()
            ->assertSee('Frontend Developer');
    }

    public function test_authenticated_user_can_apply_once(): void
    {
        $user = User::factory()->create();
        $job = Job::factory()->create();

        $this->actingAs($user)
            ->post("/jobs/{$job->id}/apply", ['cover_letter' => 'Men bu ishga qiziqaman!'])
            ->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'job_listing_id' => $job->id,
            'user_id' => $user->id,
        ]);

        // Duplicate application is blocked.
        $this->actingAs($user)
            ->post("/jobs/{$job->id}/apply", ['cover_letter' => 'Yana urinish'])
            ->assertSessionHas('errors');
    }

    public function test_guest_cannot_apply(): void
    {
        $job = Job::factory()->create();

        $this->post("/jobs/{$job->id}/apply")->assertRedirect('/login');
    }

    public function test_job_show_increments_views(): void
    {
        $job = Job::factory()->create();

        $this->get("/jobs/{$job->id}")->assertOk();
        $this->assertSame(1, $job->fresh()->views_count);
    }
}
