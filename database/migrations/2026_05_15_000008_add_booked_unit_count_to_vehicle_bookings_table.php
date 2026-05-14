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
        if (!Schema::hasTable('vehicle_bookings') || Schema::hasColumn('vehicle_bookings', 'booked_unit_count')) {
            return;
        }

        Schema::table('vehicle_bookings', function (Blueprint $table) {
            $table->unsignedInteger('booked_unit_count')->default(1)->after('gallery_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('vehicle_bookings') || !Schema::hasColumn('vehicle_bookings', 'booked_unit_count')) {
            return;
        }

        Schema::table('vehicle_bookings', function (Blueprint $table) {
            $table->dropColumn('booked_unit_count');
        });
    }
};
