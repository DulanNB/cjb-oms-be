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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name/type of the courier box
            $table->string('code')->unique(); // Unique identifier code
            $table->text('description')->nullable(); // Description of the box
            $table->decimal('length', 8, 2); // Length in cm
            $table->decimal('width', 8, 2); // Width in cm  
            $table->decimal('height', 8, 2); // Height in cm
            $table->decimal('weight_limit', 8, 2); // Maximum weight capacity in kg
            $table->decimal('volume', 12, 2); // Volume in cubic cm (calculated)
            $table->decimal('price', 10, 2); // Price for using this box type
            $table->boolean('is_active')->default(true); // Whether this box type is available
            $table->string('material')->nullable(); // Material of the box (cardboard, plastic, etc.)
            $table->string('color')->nullable(); // Color of the box
            $table->integer('stock_quantity')->default(0); // Available stock
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
