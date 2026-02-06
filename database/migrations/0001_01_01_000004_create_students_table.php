<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_no', 20)->unique();
            $table->string('library_card_no', 20)->unique();
            $table->string('lrn', 20)->nullable();
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->foreignId('grade_level_id')->nullable()->constrained('grade_levels')->onDelete('set null');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->string('school_year', 20)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_contact', 20)->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred'])->default('active');
            $table->integer('max_books_allowed')->default(3);
            $table->timestamps();
            $table->index(['last_name', 'first_name']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
