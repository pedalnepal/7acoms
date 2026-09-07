<?php

namespace App\Services;

use App\Models\Registration;
use Carbon\CarbonImmutable;

/**
 * Works out what a registration costs, from the published fee table in
 * config/registration.php.
 *
 * The amount charged is always calculated here, server-side, and never taken
 * from the browser.
 */
class RegistrationFeeCalculator
{
    /**
     * Price a registration as of the given date (defaults to today, because the
     * applicable tier is set by the date payment is made).
     *
     * @return array{
     *     currency: string,
     *     tier: string,
     *     tier_label: string,
     *     total: float,
     *     lines: array<int, array{label: string, amount: float}>
     * }
     */
    public function calculate(Registration $registration, ?CarbonImmutable $on = null): array
    {
        $on   = $on ?: CarbonImmutable::now();
        $tier = $this->tierFor($on);

        $category = $this->category($registration->category);
        $currency = $category['currency'];

        $lines = [[
            'label'  => $registration->category . ' — ' . $this->tierLabel($tier),
            'amount' => (float) $category['fees'][$tier],
        ]];

        foreach ($this->addOnLines($registration, $currency) as $line) {
            $lines[] = $line;
        }

        return [
            'currency'   => $currency,
            'tier'       => $tier,
            'tier_label' => $this->tierLabel($tier),
            'total'      => round(array_sum(array_column($lines, 'amount')), 2),
            'lines'      => $lines,
        ];
    }

    /**
     * The base fee for every category at the tier applicable today — what the
     * registration form should display next to each category, so the price
     * shown moves as a tier deadline passes or the config is edited, instead
     * of being typed in once and going stale.
     *
     * This is deliberately just the category's own fee, not a full quote: the
     * form doesn't yet know which add-ons apply, so it can only show what a
     * category costs on its own, same as it always has.
     *
     * @return array<string, array{currency: string, amount: float}>
     */
    public function currentCategoryFees(?CarbonImmutable $on = null): array
    {
        $tier = $this->tierFor($on ?: CarbonImmutable::now());

        $fees = [];

        foreach (config('registration.categories') as $name => $category) {
            $fees[$name] = [
                'currency' => $category['currency'],
                'amount'   => (float) $category['fees'][$tier],
            ];
        }

        return $fees;
    }

    /**
     * The tier whose cut-off date has not yet passed. The last tier configured
     * has no cut-off and catches everything later.
     */
    public function tierFor(CarbonImmutable $on): string
    {
        foreach (config('registration.tiers') as $key => $tier) {
            if (empty($tier['until']) || $on->lessThanOrEqualTo(CarbonImmutable::parse($tier['until'])->endOfDay())) {
                return $key;
            }
        }

        return array_key_last(config('registration.tiers'));
    }

    public function tierLabel(string $tier): string
    {
        return config("registration.tiers.$tier.label", $tier);
    }

    /**
     * Hands-on course and master class supplements. Both are zero-rated until
     * the committee publishes a price, in which case they are simply omitted.
     */
    private function addOnLines(Registration $registration, string $currency): array
    {
        $lines = [];

        if (str_contains((string) $registration->reg_for, 'Hands-on Course')) {
            $rate = (float) config("registration.hands_on_course.$currency", 0);

            if ($rate > 0) {
                $lines[] = ['label' => 'Hands-on Course', 'amount' => $rate];
            }
        }

        if (str_contains((string) $registration->reg_for, 'Master Class')) {
            $rate = (float) config("registration.master_class.$currency", 0);

            if ($rate > 0) {
                $lines[] = ['label' => 'Master Class', 'amount' => $rate];
            }
        }

        return $lines;
    }

    /**
     * @throws \InvalidArgumentException when the stored category is not priced.
     */
    private function category(?string $name): array
    {
        $category = config('registration.categories.' . $name);

        if (! $category) {
            throw new \InvalidArgumentException("No fee configured for registration category [{$name}].");
        }

        return $category;
    }
}
