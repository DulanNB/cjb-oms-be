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
        Schema::table('orders', function (Blueprint $table) {
            // Drop old columns that are no longer needed
            $table->dropForeign(['item_id']);
            $table->dropColumn(['item_id', 'price', 'address_line_1', 'address_line_2', 'state', 'postal_code', 'country', 'phone', 'email', 'weight', 'delivery_date']);
            
            // Rename 'name' to 'customer_name' for clarity
            $table->renameColumn('name', 'customer_name');
            
            // Add new fields
            $table->string('address')->nullable()->after('customer_name'); // Full address
            $table->string('city')->nullable()->change(); // Make city nullable
            $table->string('contact_number_one')->nullable()->after('city'); // Primary contact
            $table->string('contact_number_two')->nullable()->after('contact_number_one'); // Secondary contact
            $table->string('email')->nullable()->after('contact_number_two'); // Email address
            $table->text('other')->nullable()->after('email'); // Other information
            $table->date('due_date')->nullable()->after('other'); // Due date
            $table->enum('lead_from', ['facebook', 'whatsapp', 'advertisement', 'other'])->default('other')->after('due_date'); // Lead source
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Restore old columns
            $table->foreignId('item_id')->nullable()->constrained()->onDelete('restrict');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->date('delivery_date')->nullable();
            
            // Rename back
            $table->renameColumn('customer_name', 'name');
            
            // Drop new columns
            $table->dropColumn(['address', 'contact_number_one', 'contact_number_two', 'email', 'other', 'due_date', 'lead_from']);
        });
    }
};
