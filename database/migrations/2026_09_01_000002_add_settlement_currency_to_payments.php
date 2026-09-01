<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Splits what the delegate is quoted from what the card is actually charged.
 *
 * `amount` / `currency` on a registration stay the published fee — USD for
 * international categories. The charge_* columns are what the gateway is asked
 * to take, in the currency the bank settles, together with the rate used so a
 * payment can still be explained months later.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->decimal('charge_amount', 12, 2)->nullable()->after('fee_breakdown');
            $table->string('charge_currency', 3)->nullable()->after('charge_amount');
            $table->decimal('fx_rate', 15, 6)->nullable()->after('charge_currency');
            $table->date('fx_rate_date')->nullable()->after('fx_rate');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            // amount / currency on this table are the charged figures; these
            // record the fee they were converted from.
            $table->decimal('presentment_amount', 12, 2)->nullable()->after('currency');
            $table->string('presentment_currency', 3)->nullable()->after('presentment_amount');
            $table->decimal('fx_rate', 15, 6)->nullable()->after('presentment_currency');
            $table->date('fx_rate_date')->nullable()->after('fx_rate');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['charge_amount', 'charge_currency', 'fx_rate', 'fx_rate_date']);
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['presentment_amount', 'presentment_currency', 'fx_rate', 'fx_rate_date']);
        });
    }
};
