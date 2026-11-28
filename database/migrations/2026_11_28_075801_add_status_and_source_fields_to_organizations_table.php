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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('subscription_status')->default('trialing')->index();
            $table->string('registration_source')->default('self_registered')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['subscription_status']);
            $table->dropIndex(['registration_source']);
            $table->dropColumn(['subscription_status', 'registration_source', 'created_by']);
        });
    }
};
