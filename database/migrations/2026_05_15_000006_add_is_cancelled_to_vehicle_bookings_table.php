<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('vehicle_bookings') || Schema::hasColumn('vehicle_bookings', 'is_cancelled')) {
            return;
        }

        Schema::table('vehicle_bookings', function (Blueprint $table) {
            $table->boolean('is_cancelled')->default(false)->after('owner_dp_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('vehicle_bookings') || !Schema::hasColumn('vehicle_bookings', 'is_cancelled')) {
            return;
        }

        Schema::table('vehicle_bookings', function (Blueprint $table) {
            $table->dropColumn('is_cancelled');
        });
    }
};
