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
        if (!Schema::hasTable('vehicle_bookings') || Schema::hasColumn('vehicle_bookings', 'owner_dp_amount')) {
            return;
        }

        Schema::table('vehicle_bookings', function (Blueprint $table) {
            $table->decimal('owner_dp_amount', 14, 2)->default(0)->after('dp_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('vehicle_bookings') || !Schema::hasColumn('vehicle_bookings', 'owner_dp_amount')) {
            return;
        }

        Schema::table('vehicle_bookings', function (Blueprint $table) {
            $table->dropColumn('owner_dp_amount');
        });
    }
};
