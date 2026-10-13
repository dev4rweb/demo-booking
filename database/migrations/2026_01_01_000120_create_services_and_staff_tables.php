<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('price_cents');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_staff_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_member_id')->constrained()->cascadeOnDelete();
            $table->unique(['service_id', 'staff_member_id']);
        });

        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_member_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->time('starts_at');
            $table->time('ends_at');
        });

        Schema::create('staff_days_off', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_member_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('reason')->nullable();
            $table->unique(['staff_member_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_days_off');
        Schema::dropIfExists('working_hours');
        Schema::dropIfExists('service_staff_member');
        Schema::dropIfExists('staff_members');
        Schema::dropIfExists('services');
    }
};
