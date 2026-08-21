<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            // 'light' = dark text on pale green bg (default)
            // 'dark'  = light text on dark/image-heavy bg
            $table->enum('theme', ['light', 'dark'])->default('light')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn('theme');
        });
    }
};
