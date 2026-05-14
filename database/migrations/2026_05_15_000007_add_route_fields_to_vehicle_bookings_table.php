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
        if (!Schema::hasTable('vehicle_bookings')) {
            return;
        }

        Schema::table('vehicle_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_bookings', 'departure_from')) {
                $table->string('departure_from')->nullable()->after('customer_phone');
            }

            if (!Schema::hasColumn('vehicle_bookings', 'destination')) {
                $table->string('destination')->nullable()->after('departure_from');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('vehicle_bookings')) {
            return;
        }

        Schema::table('vehicle_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('vehicle_bookings', 'destination')) {
                $table->dropColumn('destination');
            }

            if (Schema::hasColumn('vehicle_bookings', 'departure_from')) {
                $table->dropColumn('departure_from');
            }
        });
    }
};
