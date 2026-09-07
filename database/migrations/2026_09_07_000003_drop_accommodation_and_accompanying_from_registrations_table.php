<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['accommodation', 'acc_rooms', 'acc_type', 'accompanying', 'acp_count']);
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('accommodation')->nullable()->after('reg_for');
            $table->unsignedInteger('acc_rooms')->nullable()->after('accommodation');
            $table->string('acc_type')->nullable()->after('acc_rooms');
            $table->string('accompanying')->nullable()->after('acc_type');
            $table->unsignedInteger('acp_count')->nullable()->after('accompanying');
        });
    }
};
