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

    public function test_the_list_offers_a_payment_status_control_except_on_trashed_rows(): void
    {
        $registration = $this->registration();

        $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index'))
            ->assertOk()
            ->assertSee('Update Payment Status')
            ->assertSee(route('registration.payment_status', $registration->id));

        $registration->delete();

        $this->actingAs($this->admin(), 'web')
            ->get(route('registration.index') . '?trashed')
            ->assertOk()
            ->assertDontSee('Update Payment Status')
            ->assertDontSee(route('registration.payment_status', $registration->id));
    }

    public function test_an_unpaid_registration_can_be_marked_paid_with_remarks(): void
    {
        $registration = $this->registration();
        $admin        = $this->admin();

        $this->actingAs($admin, 'web')
            ->post(route('registration.payment_status', $registration->id), [
                'payment_status'  => Registration::PAYMENT_PAID,
                'payment_remarks' => 'Bank transfer received 05 Sep, ref 12345.',
            ])
            ->assertRedirect(route('registration.index'));

        $registration->refresh();

        $this->assertSame(Registration::PAYMENT_PAID, $registration->payment_status);
        $this->assertSame('Bank transfer received 05 Sep, ref 12345.', $registration->payment_remarks);
        $this->assertSame($admin->id, $registration->payment_status_updated_by);
        $this->assertNotNull($registration->payment_status_updated_at);
        $this->assertNotNull($registration->paid_at);
    }

    public function test_marking_a_paid_registration_unpaid_clears_the_paid_timestamp(): void
    {
        $registration = $this->registration([
            'payment_status' => Registration::PAYMENT_PAID,
            'paid_at'        => now(),
        ]);

        $this->actingAs($this->admin(), 'web')
            ->post(route('registration.payment_status', $registration->id), [
                'payment_status'  => Registration::PAYMENT_UNPAID,
                'payment_remarks' => 'Charge reversed by the bank.',
            ])
            ->assertRedirect(route('registration.index'));

        $registration->refresh();

        $this->assertSame(Registration::PAYMENT_UNPAID, $registration->payment_status);
        $this->assertNull($registration->paid_at);
    }

    public function test_the_payment_status_change_requires_remarks(): void
    {
        $registration = $this->registration();

        $this->actingAs($this->admin(), 'web')
            ->post(route('registration.payment_status', $registration->id), [
                'payment_status'  => Registration::PAYMENT_PAID,
                'payment_remarks' => '',
            ])
            ->assertSessionHasErrors('payment_remarks');

        $this->assertSame(Registration::PAYMENT_UNPAID, $registration->refresh()->payment_status);
    }

    public function test_only_paid_and_unpaid_can_be_set_by_hand(): void
    {
        $registration = $this->registration();

        $this->actingAs($this->admin(), 'web')
            ->post(route('registration.payment_status', $registration->id), [
                'payment_status'  => Registration::PAYMENT_PENDING,
                'payment_remarks' => 'Trying to fake a gateway state.',
            ])
            ->assertSessionHasErrors('payment_status');

        $this->assertSame(Registration::PAYMENT_UNPAID, $registration->refresh()->payment_status);
    }

    public function test_a_trashed_registration_keeps_its_payment_status(): void
    {
        $registration = $this->registration();
        $registration->delete();

        $this->actingAs($this->admin(), 'web')
            ->post(route('registration.payment_status', $registration->id), [
                'payment_status'  => Registration::PAYMENT_PAID,
                'payment_remarks' => 'Should not apply.',
            ])
            ->assertRedirect(route('registration.index'));

        $this->assertSame(
            Registration::PAYMENT_UNPAID,
            Registration::withTrashed()->find($registration->id)->payment_status
        );
    }

    public function test_the_update_returns_to_the_filtered_list_it_came_from(): void
    {
        $registration = $this->registration();
        $back         = route('registration.index', ['status' => Registration::PAYMENT_UNPAID, 'page' => 2]);

        $this->actingAs($this->admin(), 'web')
            ->post(route('registration.payment_status', $registration->id), [
                'payment_status'  => Registration::PAYMENT_PAID,
                'payment_remarks' => 'Paid at the desk.',
                'back'            => $back,
            ])
            ->assertRedirect($back);
    }

    public function test_an_off_site_back_url_is_ignored(): void
    {
        $registration = $this->registration();

        $this->actingAs($this->admin(), 'web')
            ->post(route('registration.payment_status', $registration->id), [
                'payment_status'  => Registration::PAYMENT_PAID,
                'payment_remarks' => 'Paid at the desk.',
                'back'            => 'https://evil.example.com/steal',
            ])
            ->assertRedirect(route('registration.index'));
    }

    public function test_a_guest_cannot_change_a_payment_status(): void
    {
        $registration = $this->registration();

        $this->post(route('registration.payment_status', $registration->id), [
            'payment_status'  => Registration::PAYMENT_PAID,
            'payment_remarks' => 'Not signed in.',
        ])->assertRedirect(route('login'));

        $this->assertSame(Registration::PAYMENT_UNPAID, $registration->refresh()->payment_status);
    }
}
