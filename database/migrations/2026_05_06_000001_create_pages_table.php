<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 190)->nullable();
            $table->string('caption', 225)->nullable();
            $table->string('permalink', 190)->unique();
            $table->text('detail')->nullable();
            $table->integer('image')->nullable();
            $table->string('meta_title', 190)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keyword', 190)->nullable();
            $table->string('meta_robot', 190)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
