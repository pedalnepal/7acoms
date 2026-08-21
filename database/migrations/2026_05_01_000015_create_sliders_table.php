<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('language_id')->default(1);
            $table->string('title', 190)->nullable();
            $table->integer('image')->nullable();
            $table->text('caption')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->integer('menu_order')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
