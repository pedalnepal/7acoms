<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Public handle for the payment page — unguessable, so the URL can
            // be mailed to the delegate without exposing the row id.
            $table->uuid('payment_reference')->nullable()->unique()->after('status');

            $table->string('payment_status')->default('unpaid')->after('payment_reference');
            $table->decimal('amount', 12, 2)->nullable()->after('payment_status');
            $table->string('currency', 3)->nullable()->after('amount');
            $table->string('fee_tier')->nullable()->after('currency');
            $table->json('fee_breakdown')->nullable()->after('fee_tier');
            $table->timestamp('paid_at')->nullable()->after('fee_breakdown');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_reference',
                'payment_status',
                'amount',
                'currency',
                'fee_tier',
                'fee_breakdown',
                'paid_at',
            ]);
        });
    }
};
