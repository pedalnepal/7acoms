<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('registration_id')->index();

            // The clientReferenceInformation.code sent to the gateway.
            $table->string('reference')->index();

            // The gateway's own transaction id, for reconciliation in the
            // Business Center.
            $table->string('transaction_id')->nullable()->index();

            $table->string('status')->default('pending');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);

            $table->string('payment_type')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_masked')->nullable();
            $table->boolean('authenticated')->default(false);

            $table->string('reason_code')->nullable();
            $table->text('message')->nullable();

            // The gateway response, minus anything sensitive.
            $table->json('response')->nullable();

            $table->timestamps();

            $table->foreign('registration_id')
                ->references('id')->on('registrations')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
