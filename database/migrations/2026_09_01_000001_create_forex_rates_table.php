<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forex_rates', function (Blueprint $table) {
            $table->increments('id');

            $table->string('currency_iso3', 3);
            $table->string('currency_name')->nullable();

            // How many units of the foreign currency the buy/sell figures are
            // quoted for. 1 for USD, but 10 for JPY and 100 for INR — dividing
            // by this is what keeps the conversion honest.
            $table->unsignedSmallInteger('unit')->default(1);

            $table->decimal('buy', 15, 6);
            $table->decimal('sell', 15, 6);

            // The date the rates apply to, as opposed to when NRB published or
            // last amended them.
            $table->date('rate_date');
            $table->date('published_on')->nullable();
            $table->date('modified_on')->nullable();

            $table->timestamps();

            $table->unique(['currency_iso3', 'rate_date']);
            $table->index('rate_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forex_rates');
    }
};
