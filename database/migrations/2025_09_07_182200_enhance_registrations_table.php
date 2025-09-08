<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Add missing timestamps (created_at, updated_at)
            if (!Schema::hasColumn('registrations', 'created_at')) {
                $table->timestamps();
            }
            
            // Add registration status
            if (!Schema::hasColumn('registrations', 'status')) {
                $table->enum('status', ['confirmed', 'cancelled', 'attended', 'no_show'])->default('confirmed')->after('registered_at');
            }
            
            // Add payment information
            if (!Schema::hasColumn('registrations', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending')->after('status');
            }
            
            if (!Schema::hasColumn('registrations', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_status');
            }
            
            if (!Schema::hasColumn('registrations', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('registrations', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0.00)->after('payment_reference');
            }
            
            // Add additional registration fields
            if (!Schema::hasColumn('registrations', 'notes')) {
                $table->text('notes')->nullable()->after('amount_paid');
            }
            
            if (!Schema::hasColumn('registrations', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('registrations', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('checked_in_at');
            }
            
            if (!Schema::hasColumn('registrations', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
            
            // Add soft deletes
            if (!Schema::hasColumn('registrations', 'deleted_at')) {
                $table->softDeletes();
            }
            
            // Add performance indexes
            $table->index(['status']);
            $table->index(['payment_status']);
            $table->index(['registered_at']);
            $table->index(['checked_in_at']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
{
    Schema::table('registrations', function (Blueprint $table) {
        // Drop indexes first
        $table->dropIndex('registrations_status_index');
        $table->dropIndex('registrations_payment_status_index');
        $table->dropIndex('registrations_registered_at_index');
        $table->dropIndex('registrations_checked_in_at_index');
        $table->dropIndex('registrations_created_at_index');

        // Then drop columns
        $table->dropColumn([
            'status',
            'payment_status',
            'payment_method',
            'payment_reference',
            'amount_paid',
            'notes',
            'checked_in_at',
            'cancelled_at',
            'cancellation_reason',
            'created_at',
            'updated_at',
            'deleted_at'
        ]);

            
            $table->dropIndex(['registrations_status_index']);
            $table->dropIndex(['registrations_payment_status_index']);
            $table->dropIndex(['registrations_registered_at_index']);
            $table->dropIndex(['registrations_checked_in_at_index']);
            $table->dropIndex(['registrations_created_at_index']);
        });
    }
};
