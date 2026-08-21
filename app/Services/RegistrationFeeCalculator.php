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

        foreach ($this->accompanyingLines($registration, $tier, $currency) as $line) {
            $lines[] = $line;
        }

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
     * Accompanying people are billed per head, at the accompanying rate that
     * matches the delegate's own currency. A delegate who registered *as* an
     * accompanying person is not charged again.
     */
    private function accompanyingLines(Registration $registration, string $tier, string $currency): array
    {
        $count = (int) $registration->acp_count;

        if (strcasecmp((string) $registration->accompanying, 'Yes') !== 0 || $count < 1) {
            return [];
        }

        if (in_array($registration->category, config('registration.accompanying_only_categories'), true)) {
            return [];
        }

        $name = config("registration.accompanying_category.$currency");

        if (! $name) {
            return [];
        }

        $rate = (float) $this->category($name)['fees'][$tier];

        if ($rate <= 0) {
            return [];
        }

        return [[
            'label'  => $name . ' × ' . $count,
            'amount' => $rate * $count,
        ]];
    }

    /**
     * Hands-on course and accommodation supplements. Both are zero-rated until
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

        $rooms = (int) $registration->acc_rooms;

        if (strcasecmp((string) $registration->accommodation, 'Yes') === 0 && $rooms > 0 && $registration->acc_type) {
            $rate = (float) config("registration.accommodation.{$registration->acc_type}.$currency", 0);

            if ($rate > 0) {
                $lines[] = [
                    'label'  => 'Accommodation — ' . $registration->acc_type . ' × ' . $rooms,
                    'amount' => $rate * $rooms,
                ];
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
