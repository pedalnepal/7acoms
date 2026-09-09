<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Why a payment was marked paid or unpaid by hand — bank transfers
            // settled offline, refunds, corrections — kept next to the status
            // it explains, together with who recorded it.
            $table->text('payment_remarks')->nullable()->after('paid_at');
            $table->unsignedBigInteger('payment_status_updated_by')->nullable()->after('payment_remarks');
            $table->timestamp('payment_status_updated_at')->nullable()->after('payment_status_updated_by');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_remarks',
                'payment_status_updated_by',
                'payment_status_updated_at',
            ]);
        });
    }
};
