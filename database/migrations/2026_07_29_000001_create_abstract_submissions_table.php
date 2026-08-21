<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abstract_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('authors')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('presenting_author')->nullable();
            $table->string('designation')->nullable();
            $table->string('category')->nullable();
            $table->string('pres_type')->nullable();
            $table->string('research_type')->nullable();
            $table->string('pres_category')->nullable();
            $table->text('abstract_body')->nullable();
            $table->text('reference_list')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status')->default('submitted');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abstract_submissions');
    }
};
