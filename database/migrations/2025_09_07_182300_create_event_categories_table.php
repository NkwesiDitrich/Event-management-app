<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#007bff'); // Hex color code
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active']);
            $table->index(['sort_order']);
        });
        
        // Add category relationship to events table
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('event_categories')->onDelete('set null')->after('organizer_id');
                $table->index(['category_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
        
        Schema::dropIfExists('event_categories');
    }
};
