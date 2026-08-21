<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id');
            $table->integer('parent_id')->default(0);
            $table->string('menu_title', 190)->nullable();
            $table->string('menu_link', 190)->nullable();
            $table->text('custom_link')->nullable();
            $table->string('menu_class', 190)->nullable();
            $table->string('menu_target', 190)->nullable();
            $table->integer('menu_order')->nullable();
            $table->string('link_type', 190)->nullable();
            $table->string('dbname', 190)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
