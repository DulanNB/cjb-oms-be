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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Customer name
            $table->string('order_number')->unique(); // Unique order identifier
            $table->foreignId('item_id')->constrained()->onDelete('restrict'); // Relationship to items table
            $table->decimal('price', 10, 2); // Order price
            $table->string('status')->default('pending'); // Order status: pending, processing, shipped, delivered, cancelled
            
            // Address fields
            $table->string('address_line_1'); // Street address
            $table->string('address_line_2')->nullable(); // Apartment, suite, etc.
            $table->string('city'); // City
            $table->string('state')->nullable(); // State/Province
            $table->string('postal_code'); // ZIP/Postal code
            $table->string('country'); // Country
            
            // Additional fields
            $table->string('phone')->nullable(); // Contact phone number
            $table->string('email')->nullable(); // Contact email
            $table->text('notes')->nullable(); // Additional notes/instructions
            $table->decimal('weight', 8, 2)->nullable(); // Package weight in kg
            $table->date('delivery_date')->nullable(); // Expected/actual delivery date
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
