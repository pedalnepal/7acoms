<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminRegistrationListTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function registration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'full_name'         => 'Test Delegate',
            'email'             => 'delegate@example.com',
            'phone'             => '9800000000',
            'category'          => 'NAOMS Member',
            'status'            => 'pending',
            'payment_reference' => (string) Str::uuid(),
            'payment_status'    => Registration::PAYMENT_UNPAID,
        ], $overrides));
    }

    public function test_the_list_is_paginated_at_ten_per_page(): void
    {
        for ($n = 0; $n < 15; $n++) {
            $this->registration(['email' => "delegate{$n}@example.com", 'payment_reference' => (string) Str::uuid()]);
        }

        $response = $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index'))
            ->assertOk();

        $this->assertCount(10, $response->viewData('registrations')->items());
        $this->assertSame(15, $response->viewData('registrations')->total());
    }

    public function test_the_second_page_shows_the_remainder(): void
    {
        for ($n = 0; $n < 15; $n++) {
            $this->registration(['email' => "delegate{$n}@example.com", 'payment_reference' => (string) Str::uuid()]);
        }

        $response = $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index', ['page' => 2]))
            ->assertOk();

        $this->assertCount(5, $response->viewData('registrations')->items());
    }

    public function test_the_payment_reference_column_shows_the_delegate_facing_code(): void
    {
        $registration = $this->registration();

        $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index'))
            ->assertOk()
            ->assertSee($registration->paymentCode());
    }

    public function test_search_matches_name_email_phone_or_payment_code(): void
    {
        $match = $this->registration(['full_name' => 'Alice Example', 'email' => 'alice@example.com']);
        $other = $this->registration(['full_name' => 'Bob Other', 'email' => 'bob@example.com']);

        $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index', ['q' => 'Alice']))
            ->assertOk()
            ->assertSee($match->full_name)
            ->assertDontSee($other->full_name);

        $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index', ['q' => $match->paymentCode()]))
            ->assertOk()
            ->assertSee($match->full_name)
            ->assertDontSee($other->full_name);
    }

    public function test_status_filter_narrows_to_matching_registrations(): void
    {
        $paid   = $this->registration(['full_name' => 'Paid Delegate', 'email' => 'paid@example.com', 'payment_status' => Registration::PAYMENT_PAID]);
        $failed = $this->registration(['full_name' => 'Failed Delegate', 'email' => 'failed@example.com', 'payment_status' => Registration::PAYMENT_FAILED]);

        $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index', ['status' => Registration::PAYMENT_PAID]))
            ->assertOk()
            ->assertSee($paid->full_name)
            ->assertDontSee($failed->full_name);
    }

    public function test_a_soft_deleted_registration_is_hidden_unless_trashed_is_requested(): void
    {
        $registration = $this->registration();
        $registration->delete();

        $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index'))
            ->assertOk()
            ->assertDontSee($registration->full_name);

        $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index') . '?trashed')
            ->assertOk()
            ->assertSee($registration->full_name);
    }

    public function test_a_guest_is_redirected_away_from_the_list(): void
    {
        $this->get(route('registration.index'))->assertRedirect(route('login'));
    }
}
