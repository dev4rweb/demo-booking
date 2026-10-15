<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_members', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable()->after('is_active');
            $table->string('telegram_connect_token')->nullable()->unique()->after('telegram_chat_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('locale', 8)->default('en')->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('staff_members', function (Blueprint $table) {
            $table->dropColumn(['telegram_chat_id', 'telegram_connect_token']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
