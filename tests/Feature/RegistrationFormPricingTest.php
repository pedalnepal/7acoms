<?php

namespace Tests\Feature;

use App\Models\Page;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The registration form used to have each category's fee typed directly into
 * the Blade template, frozen at whatever the early-bird price was on the day
 * someone wrote it. It's now sourced from RegistrationFeeCalculator, so it
 * should track config/registration.php and the current tier.
 */
class RegistrationFormPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Page::create(['title' => 'Registration Form', 'permalink' => 'registration-form']);

        // Pinned so the suite doesn't depend on the ambient config/registration.php
        // being edited later. The real config already has this exact shape —
        // see config/registration.php.
        config([
            'registration.tiers' => [
                'early'   => ['label' => 'Early Bird Registration', 'until' => '2026-11-15'],
                'regular' => ['label' => 'Registration', 'until' => '2027-01-15'],
                'late'    => ['label' => 'Late Registration', 'until' => null],
            ],
        ]);
        config(['registration.categories.NAOMS Member' => [
            'currency' => 'NPR',
            'fees'     => ['early' => 18000, 'regular' => 20000, 'late' => 22000],
        ]]);
        config(['registration.categories.International Delegate' => [
            'currency' => 'USD',
            'fees'     => ['early' => 200, 'regular' => 240, 'late' => 260],
        ]]);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    /**
     * The fee shown next to one category's radio button. Scoped to that
     * category specifically, rather than searching the whole page — several
     * categories share the same numeral at different tiers (e.g. Non-NAOMS
     * Member's early fee is NAOMS Member's regular fee), so a page-wide
     * "does this text appear anywhere" check would be a false signal.
     */
    private function feeShownFor(string $content, string $radioId): string
    {
        preg_match('/id="' . preg_quote($radioId, '/') . '".*?cat-fee">(.*?)<\/span>/s', $content, $matches);

        return $matches[1] ?? '(not found)';
    }

    public function test_the_form_shows_the_early_bird_price_before_the_first_deadline(): void
    {
        CarbonImmutable::setTestNow('2026-10-01');

        $content = $this->get(route('registration.form'))->assertOk()->getContent();

        $this->assertSame('NPR 18,000', $this->feeShownFor($content, 'cat-1'));
        $this->assertSame('USD 200', $this->feeShownFor($content, 'cat-3'));
    }

    public function test_the_form_moves_to_the_regular_price_once_the_deadline_passes(): void
    {
        CarbonImmutable::setTestNow('2026-12-01');

        $content = $this->get(route('registration.form'))->assertOk()->getContent();

        $this->assertSame('NPR 20,000', $this->feeShownFor($content, 'cat-1'));
        $this->assertSame('USD 240', $this->feeShownFor($content, 'cat-3'));
    }

    public function test_the_form_reflects_a_config_change_without_any_code_change(): void
    {
        CarbonImmutable::setTestNow('2026-10-01');

        // The organising committee revises the early-bird fee.
        config(['registration.categories.NAOMS Member.fees.early' => 19500]);

        $content = $this->get(route('registration.form'))->assertOk()->getContent();

        $this->assertSame('NPR 19,500', $this->feeShownFor($content, 'cat-1'));
    }
}
