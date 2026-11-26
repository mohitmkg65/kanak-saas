<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->string('name');
            $table->string('slug');
            $table->string('scope')->index(); // SUPER_ADMIN or ORGANIZATION
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'slug']);
            $table->index(['scope', 'organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
