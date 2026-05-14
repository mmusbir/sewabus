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
            if (!Schema::hasColumn('rental_packages', 'itinerary')) {
                $table->json('itinerary')->nullable()->after('includes');
            }

            if (!Schema::hasColumn('rental_packages', 'excludes')) {
                $table->text('excludes')->nullable()->after('itinerary');
            }

            if (!Schema::hasColumn('rental_packages', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('excludes');
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
                Schema::hasColumn('rental_packages', 'terms_conditions') ? 'terms_conditions' : null,
                Schema::hasColumn('rental_packages', 'excludes') ? 'excludes' : null,
                Schema::hasColumn('rental_packages', 'itinerary') ? 'itinerary' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};

