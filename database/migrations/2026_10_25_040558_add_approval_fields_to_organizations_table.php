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
            $table->string('approval_status')->default('pending')->after('status');
            $table->text('rejection_reason')->nullable()->after('approval_status');
            $table->string('owner_email')->nullable()->after('rejection_reason');
            $table->timestamp('approved_at')->nullable()->after('owner_email');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');

            $table->index('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['approval_status']);
            $table->dropColumn(['approval_status', 'rejection_reason', 'owner_email', 'approved_at', 'approved_by']);
        });
    }
};
