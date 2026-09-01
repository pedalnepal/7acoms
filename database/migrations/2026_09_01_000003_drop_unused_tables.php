<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drops four tables that were created early in the project but never wired
 * up to anything, confirmed empty in production before this migration was
 * written:
 *
 *  - contacts: no model, no controller writes to it, no route submits to it,
 *    and the contact page's form has no action. The ContactSent mailable and
 *    emails.contact view it would have paired with are dispatched nowhere.
 *  - countries: the Country model was never queried anywhere; the
 *    registration form's nationality field is a hardcoded radio group, not
 *    sourced from this table.
 *  - personal_access_tokens: scaffolded for Sanctum, which was never added
 *    as a dependency — no HasApiTokens trait exists in the codebase.
 *  - categories: no model, no admin controller/routes/views ever created a
 *    row. Its only reference was a dropdown option in the menu admin that
 *    could never have resolved to real data.
 *
 * down() recreates each table's original schema, so this stays reversible
 * even though the migrations that first created them are gone.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('categories');
    }

    public function down(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address')->nullable();
            $table->text('message')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->char('iso', 2);
            $table->string('name', 80);
            $table->string('nicename', 80);
            $table->char('iso3', 3)->nullable();
            $table->smallInteger('numcode')->nullable();
            $table->integer('phonecode');
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('permalink')->unique();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('image')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keyword')->nullable();
            $table->string('meta_robot')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'sort_order']);
        });
    }
};
