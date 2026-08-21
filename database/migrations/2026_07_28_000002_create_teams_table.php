<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 190)->nullable();
            $table->string('permalink', 190)->unique();
            $table->integer('team_category_id')->nullable()->index();
            $table->string('designation', 190)->nullable();
            $table->text('detail')->nullable();
            $table->integer('image')->nullable();
            $table->tinyInteger('show_on_leadership')->default(0);
            $table->integer('menu_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
