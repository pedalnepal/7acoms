<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abstract_submissions', function (Blueprint $table) {
            $table->string('email')->nullable()->after('presenting_author');
        });
    }

    public function down(): void
    {
        Schema::table('abstract_submissions', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
