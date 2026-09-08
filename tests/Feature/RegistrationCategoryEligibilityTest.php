<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The registration form offers only the categories that match the declared
 * nationality and NAOMS membership. The map lives in config/registration.php
 * so the form and the store request cannot drift apart.
 */
class RegistrationCategoryEligibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Page::create(['title' => 'Registration Form', 'permalink' => 'registration-form']);
    }

    /**
     * Read straight from the file: a data provider runs before the application
     * is booted, so the config repository is not available to it yet.
     *
     * @return array<string, mixed>
     */
    private static function config(): array
    {
        return require __DIR__ . '/../../config/registration.php';
    }

    /**
     * @param  array<string, string>  $fields
     * @return array<string, mixed>
     */
    private function payload(array $fields = []): array
    {
        return array_merge([
            'fullName'    => 'Test Delegate',
            'email'       => 'delegate@example.com',
            'phone'       => '9800000000',
            'designation' => 'Consultant/Faculty',
            'workplace'   => 'Test Hospital',
            'nationality' => 'Nepali',
            'naomsMember' => 'Yes',
            'regFor'      => 'Conference',
            'category'    => 'NAOMS Member',
        ], $fields);
    }

    public function test_the_form_carries_the_eligibility_map_for_every_answer_pair(): void
    {
        $response = $this->get(route('registration.form'))->assertOk();

        $this->assertSame(
            config('registration.eligibility'),
            $response->viewData('categoryEligibility')
        );
    }

    /**
     * Every category is rendered; the page narrows them to the eligible set
     * once the delegate answers, so the form still works without JavaScript.
     */
    public function test_the_form_still_renders_every_configured_category(): void
    {
        $response = $this->get(route('registration.form'))->assertOk();

        foreach (array_keys(config('registration.categories')) as $category) {
            $response->assertSee($category, false);
        }
    }

    #[DataProvider('eligiblePairs')]
    public function test_an_eligible_category_is_accepted(string $nationality, string $member, string $category): void
    {
        $this->post(route('registration.store'), $this->payload([
            'nationality' => $nationality,
            'naomsMember' => $member,
            'category'    => $category,
        ]))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('registrations', [
            'nationality'  => $nationality,
            'naoms_member' => $member,
            'category'     => $category,
        ]);
    }

    public static function eligiblePairs(): array
    {
        $cases = [];

        foreach (self::config()['eligibility'] as $nationality => $byMembership) {
            foreach ($byMembership as $member => $categories) {
                foreach ($categories as $category) {
                    $cases["{$nationality} / {$member} / {$category}"] = [$nationality, $member, $category];
                }
            }
        }

        return $cases;
    }

    /**
     * The form never shows these pairings, so a post carrying one has been
     * tampered with — an international delegate must not reach a Nepalese
     * rate quoted in NPR, which is a fraction of the USD fee.
     */
    #[DataProvider('ineligiblePairs')]
    public function test_an_ineligible_category_is_rejected(string $nationality, string $member, string $category): void
    {
        $this->post(route('registration.store'), $this->payload([
            'nationality' => $nationality,
            'naomsMember' => $member,
            'category'    => $category,
        ]))->assertSessionHasErrors('category');

        $this->assertSame(0, Registration::count());
    }

    public static function ineligiblePairs(): array
    {
        return [
            'international delegate claiming the NAOMS member rate' => ['International', 'Yes', 'NAOMS Member'],
            'international delegate claiming a Nepalese resident rate' => ['International', 'No', 'Residents and Dental Surgeons (Nepalese)'],
            'international delegate claiming a Nepalese companion rate' => ['International', 'Yes', 'Accompanying Person'],
            'nepali member claiming the non-member rate' => ['Nepali', 'Yes', 'Non-NAOMS Member (Nepalese)'],
            'nepali non-member claiming the member rate' => ['Nepali', 'No', 'NAOMS Member'],
            'nepali delegate claiming the international rate' => ['Nepali', 'Yes', 'International Delegate'],
        ];
    }
}
