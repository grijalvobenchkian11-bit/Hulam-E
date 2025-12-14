<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Auth-critical
            $table->string('role')->default('user')->change();
            $table->boolean('verified')->default(false)->change();
            $table->string('verification_status')->default('unverified')->change();

            // Profile / optional fields
            $table->integer('profile_completion')->default(0)->change();
            $table->decimal('rating', 3, 1)->nullable()->change();
            $table->integer('total_ratings')->default(0)->change();
            $table->boolean('is_online')->default(false)->change();

            // Make optional fields nullable
            $table->text('bio')->nullable()->change();
            $table->string('contact_number')->nullable()->change();
            $table->string('course_year')->nullable()->change();
            $table->date('birthday')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('social_link')->nullable()->change();
            $table->string('profile_picture')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->string('website')->nullable()->change();
            $table->json('skills')->nullable()->change();
            $table->string('education')->nullable()->change();

            // Verification fields
            $table->string('verification_document')->nullable()->change();
            $table->string('verification_document_type')->nullable()->change();
            $table->timestamp('verification_submitted_at')->nullable()->change();
            $table->timestamp('verification_reviewed_at')->nullable()->change();
            $table->text('verification_notes')->nullable()->change();
            $table->foreignId('verified_by')->nullable()->change();
        });
    }

    public function down(): void {}
};
