<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Add 'pending' status for admin approval workflow
            $table->dropColumn('status');
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending', 'published', 'cancelled'])->default('draft')->after('current_registrations');
            
            // Add additional event fields
            if (!Schema::hasColumn('events', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('events', 'price')) {
                $table->decimal('price', 10, 2)->default(0.00)->after('capacity');
            }
            
            if (!Schema::hasColumn('events', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('price');
            }
            
            if (!Schema::hasColumn('events', 'tags')) {
                $table->json('tags')->nullable()->after('location');
            }
            
            if (!Schema::hasColumn('events', 'requirements')) {
                $table->text('requirements')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('events', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('events', 'registration_deadline')) {
                $table->dateTime('registration_deadline')->nullable()->after('event_date');
            }
            
            if (!Schema::hasColumn('events', 'min_attendees')) {
                $table->integer('min_attendees')->nullable()->after('capacity');
            }
            
            // Add soft deletes
            if (!Schema::hasColumn('events', 'deleted_at')) {
                $table->softDeletes();
            }
            
            // Add performance indexes
            $table->index(['is_featured']);
            $table->index(['price']);
            $table->index(['registration_deadline']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'image', 
                'price', 
                'currency', 
                'tags', 
                'requirements', 
                'is_featured', 
                'registration_deadline', 
                'min_attendees',
                'deleted_at'
            ]);
            
            $table->dropIndex(['events_is_featured_index']);
            $table->dropIndex(['events_price_index']);
            $table->dropIndex(['events_registration_deadline_index']);
            $table->dropIndex(['events_created_at_index']);
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft');
        });
    }
};
