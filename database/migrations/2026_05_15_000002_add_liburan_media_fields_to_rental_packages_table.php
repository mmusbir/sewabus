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
        Schema::table('rental_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('rental_packages', 'vehicle_exterior_image_path')) {
                $table->string('vehicle_exterior_image_path')->nullable()->after('image_path');
            }

            if (!Schema::hasColumn('rental_packages', 'vehicle_interior_image_path')) {
                $table->string('vehicle_interior_image_path')->nullable()->after('vehicle_exterior_image_path');
            }

            if (!Schema::hasColumn('rental_packages', 'lodging_exterior_image_path')) {
                $table->string('lodging_exterior_image_path')->nullable()->after('vehicle_interior_image_path');
            }

            if (!Schema::hasColumn('rental_packages', 'lodging_interior_image_path')) {
                $table->string('lodging_interior_image_path')->nullable()->after('lodging_exterior_image_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_packages', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('rental_packages', 'lodging_interior_image_path') ? 'lodging_interior_image_path' : null,
                Schema::hasColumn('rental_packages', 'lodging_exterior_image_path') ? 'lodging_exterior_image_path' : null,
                Schema::hasColumn('rental_packages', 'vehicle_interior_image_path') ? 'vehicle_interior_image_path' : null,
                Schema::hasColumn('rental_packages', 'vehicle_exterior_image_path') ? 'vehicle_exterior_image_path' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};

