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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Relationship to orders table
            $table->foreignId('product_id')->constrained('items')->onDelete('restrict'); // Relationship to items table
            $table->integer('qty')->default(1); // Quantity
            $table->decimal('sale_amount', 10, 2); // Sale amount per item
            $table->decimal('del_fee', 10, 2)->default(0); // Delivery fee
            $table->boolean('is_invoiced')->default(false); // Is invoice generated for this item
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
