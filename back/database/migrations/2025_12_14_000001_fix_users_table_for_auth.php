<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Role
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user');
            }

            // Verification flags
            if (!Schema::hasColumn('users', 'verified')) {
                $table->boolean('verified')->default(false);
            }

            if (!Schema::hasColumn('users', 'verification_status')) {
                $table->string('verification_status')->default('active');
            }

            // Optional profile fields (nullable to avoid crashes)
            $nullableStrings = [
                'bio',
                'contact_number',
                'course_year',
                'gender',
                'social_link',
                'profile_picture',
                'location',
                'website',
                'education',
                'verification_document',
                'verification_document_type',
                'verification_notes',
                'verified_by',
            ];

            foreach ($nullableStrings as $column) {
                if (!Schema::hasColumn('users', $column)) {
                    $table->string($column)->nullable();
                }
            }

            // JSON / numeric fields
            if (!Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable();
            }

            if (!Schema::hasColumn('users', 'rating')) {
                $table->decimal('rating', 3, 1)->default(0);
            }

            if (!Schema::hasColumn('users', 'total_ratings')) {
                $table->integer('total_ratings')->default(0);
            }

            // Status flags
            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false);
            }

            if (!Schema::hasColumn('users', 'show_email')) {
                $table->boolean('show_email')->default(true);
            }

            if (!Schema::hasColumn('users', 'show_contact')) {
                $table->boolean('show_contact')->default(true);
            }

            if (!Schema::hasColumn('users', 'show_social_link')) {
                $table->boolean('show_social_link')->default(true);
            }

            if (!Schema::hasColumn('users', 'profile_completion')) {
                $table->integer('profile_completion')->default(0);
            }

            // Dates
            if (!Schema::hasColumn('users', 'birthday')) {
                $table->date('birthday')->nullable();
            }

            if (!Schema::hasColumn('users', 'last_seen')) {
                $table->timestamp('last_seen')->nullable();
            }

            if (!Schema::hasColumn('users', 'verification_submitted_at')) {
                $table->timestamp('verification_submitted_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'verification_reviewed_at')) {
                $table->timestamp('verification_reviewed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        // No rollback – production safety
    }
};
