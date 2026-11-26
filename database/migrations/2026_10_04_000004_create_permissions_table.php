<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->string('module')->index();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->string('scope')->index(); // SUPER_ADMIN or ORGANIZATION
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
