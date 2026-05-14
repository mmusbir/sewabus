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
            if (!Schema::hasColumn('vehicle_bookings', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('booking_code');
            }

            if (!Schema::hasColumn('vehicle_bookings', 'customer_phone')) {
                $table->string('customer_phone', 25)->nullable()->after('customer_name');
            }

            if (!Schema::hasColumn('vehicle_bookings', 'pickup_location')) {
                $table->text('pickup_location')->nullable()->after('customer_phone');
            }

            if (!Schema::hasColumn('vehicle_bookings', 'pickup_time')) {
                $table->string('pickup_time', 10)->nullable()->after('pickup_location');
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
            $columns = [
                'customer_name',
                'customer_phone',
                'pickup_location',
                'pickup_time',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('vehicle_bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
