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
        Schema::create('organization_products', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();

            // If linked to global catalog, product_id is set. If custom org product, null.
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            // Product details — for custom products these are required,
            // for catalog products they override the global values
            $table->string('name');
            $table->string('sku')->nullable();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('hsn_code')->nullable();
            $table->string('brand')->nullable();
            $table->text('description')->nullable();

            // Organization-specific pricing
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('mrp', 12, 2)->nullable();

            // Stock & inventory basics (for future inventory module)
            $table->decimal('opening_stock', 12, 2)->default(0);
            $table->decimal('min_stock_level', 12, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index('organization_id');
            $table->index('product_id');
            $table->index('category_id');
            $table->index('is_active');
            $table->unique(['organization_id', 'sku'], 'org_product_sku_unique');
            $table->unique(['organization_id', 'product_id'], 'org_product_catalog_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_products');
    }
};
