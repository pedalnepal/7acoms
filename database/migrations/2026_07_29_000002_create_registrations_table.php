<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->date('reg_date')->nullable();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('designation')->nullable();
            $table->string('workplace')->nullable();
            $table->string('id_card_name')->nullable();
            $table->string('id_card_path')->nullable();
            $table->string('nationality')->nullable();
            $table->string('naoms_member')->nullable();
            $table->string('member_id')->nullable();
            $table->string('reg_for')->nullable();
            $table->string('accommodation')->nullable();
            $table->unsignedInteger('acc_rooms')->nullable();
            $table->string('acc_type')->nullable();
            $table->string('accompanying')->nullable();
            $table->unsignedInteger('acp_count')->nullable();
            $table->string('category')->nullable();
            $table->string('receipt_name')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('others')->nullable();
            $table->string('status')->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
