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
        Schema::create('vehicle_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone', 25);
            $table->string('departure_from');
            $table->string('destination');
            $table->text('pickup_location');
            $table->string('pickup_time', 10);
            $table->date('departure_date');
            $table->date('return_date');
            $table->string('service_type');
            $table->string('service_type_note')->nullable();
            $table->string('po_key')->nullable()->index();
            $table->foreignId('gallery_id')->nullable()->constrained('galleries')->nullOnDelete();
            $table->decimal('deal_price', 14, 2)->default(0);
            $table->decimal('markup_price', 14, 2)->default(0);
            $table->decimal('dp_amount', 14, 2)->default(0);
            $table->decimal('owner_dp_amount', 14, 2)->default(0);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->index(['departure_date', 'return_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_bookings');
    }
};
