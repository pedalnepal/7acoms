<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->renameColumn('id_card_name', 'recommendation_letter_name');
            $table->renameColumn('id_card_path', 'recommendation_letter_path');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->renameColumn('recommendation_letter_name', 'id_card_name');
            $table->renameColumn('recommendation_letter_path', 'id_card_path');
        });
    }
};
