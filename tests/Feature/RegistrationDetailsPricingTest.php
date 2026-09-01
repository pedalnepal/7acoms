<?php

namespace Tests\Feature;

use App\Models\Page;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The fee table on registration-details used to be static HTML, hand-typed
 * to match config/registration.php on the day it was written — including
 * which tier's column was highlighted as "currently active". It's now
 * sourced from the same config and the same RegistrationFeeCalculator the
 * registration form and the payment page use, so all three can never quote
 * a different price or disagree about which tier is in effect.
 */
class RegistrationDetailsPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Page::create(['title' => 'Registration Details', 'permalink' => 'registration-details']);

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
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    /** The fee cell for one category, under one tier's column. */
    private function feeCellFor(string $content, string $category, int $columnIndex): string
    {
        preg_match(
            '/<th scope="row">' . preg_quote($category, '/') . '<\/th>(.*?)<\/tr>/s',
            $content,
            $row
        );

        preg_match_all('/<td[^>]*>(.*?)<\/td>/s', $row[1] ?? '', $cells);

        return trim(preg_replace('/\s+/', ' ', strip_tags($cells[1][$columnIndex] ?? '(not found)')));
    }

    public function test_the_early_bird_column_is_highlighted_before_the_first_deadline(): void
    {
        CarbonImmutable::setTestNow('2026-10-01');

        $content = $this->get(route('registration.details'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/deadline-card is-active">\s*<div class="dl-label">Early Bird Registration/',
            $content
        );
        $this->assertSame('NPR18,000', $this->feeCellFor($content, 'NAOMS Member', 0));
    }

    public function test_the_regular_column_is_highlighted_once_the_deadline_passes(): void
    {
        CarbonImmutable::setTestNow('2026-12-01');

        $content = $this->get(route('registration.details'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/deadline-card is-active">\s*<div class="dl-label">Registration</',
            $content
        );
        $this->assertSame('NPR20,000', $this->feeCellFor($content, 'NAOMS Member', 1));
    }

    /**
     * The two pages a delegate actually compares prices on must never
     * disagree, at any date, for any category priced in config.
     */
    public function test_the_details_page_and_the_registration_form_quote_the_same_price(): void
    {
        Page::create(['title' => 'Registration Form', 'permalink' => 'registration-form']);

        foreach (['2026-10-01' => 0, '2026-12-01' => 1, '2027-02-01' => 2] as $date => $column) {
            CarbonImmutable::setTestNow($date);

            $detailsFee = $this->feeCellFor(
                $this->get(route('registration.details'))->getContent(),
                'NAOMS Member',
                $column
            );

            $formContent = $this->get(route('registration.form'))->getContent();
            preg_match('/id="cat-1".*?cat-fee">(.*?)<\/span>/s', $formContent, $formMatch);
            $formFee = str_replace(' ', '', $formMatch[1] ?? '(not found)');

            $this->assertSame($detailsFee, $formFee, "Mismatch on {$date}");
        }
    }
}
