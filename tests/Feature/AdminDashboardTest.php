<?php

namespace Tests\Feature;

use App\Models\AbstractSubmission;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any authenticated admin (web) user reaches the dashboard — see
     * AppServiceProvider::boot(), which grants every ability to an
     * App\Models\User regardless of assigned permissions.
     */
    private function admin(): User
    {
        return User::factory()->create();
    }

    private function registration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'full_name'         => 'Test Delegate',
            'email'             => 'delegate@example.com',
            'category'          => 'NAOMS Member',
            'status'            => 'pending',
            'payment_reference' => (string) Str::uuid(),
            'payment_status'    => Registration::PAYMENT_UNPAID,
        ], $overrides));
    }

    private function abstractSubmission(array $overrides = []): AbstractSubmission
    {
        return AbstractSubmission::create(array_merge([
            'title'             => 'A study of something',
            'authors'           => 'A. Author',
            'presenting_author' => 'A. Author',
            'email'             => 'author@example.com',
            'status'            => 'submitted',
        ], $overrides));
    }

    public function test_the_dashboard_shows_total_registrations_and_abstracts(): void
    {
        $this->registration();
        $this->registration(['email' => 'two@example.com', 'payment_reference' => (string) Str::uuid()]);
        $this->abstractSubmission();

        $this->actingAs($this->admin(), 'web')
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total Registrations')
            ->assertSee('Total Abstract Submissions')
            ->assertSeeInOrder(['2', 'Total Registrations'])
            ->assertSeeInOrder(['1', 'Total Abstract Submissions']);
    }

    public function test_a_soft_deleted_registration_or_abstract_is_not_counted(): void
    {
        $this->registration()->delete();
        $this->abstractSubmission()->delete();

        $this->actingAs($this->admin(), 'web')
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['0', 'Total Registrations'])
            ->assertSeeInOrder(['0', 'Total Abstract Submissions']);
    }

    /**
     * Per AppServiceProvider::boot(), any authenticated admin (web) user has
     * full access regardless of assigned permissions — Gate::before() grants
     * every ability to an App\Models\User. The dashboard's own
     * `permission:view dashboard` gate is therefore satisfied by
     * authentication alone; a guest is still turned away.
     */
    public function test_any_authenticated_admin_can_reach_the_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'web')
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_a_guest_is_redirected_away_from_the_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
