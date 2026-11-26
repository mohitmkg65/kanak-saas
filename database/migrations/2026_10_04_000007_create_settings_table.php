<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('scope')->index(); // SUPER_ADMIN or ORGANIZATION
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('value_type')->default('string');
            $table->string('group')->default('general')->index();
            $table->timestamps();

            $table->unique(['scope', 'organization_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
