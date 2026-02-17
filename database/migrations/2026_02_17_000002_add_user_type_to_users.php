<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['librarian', 'student_assistant', 'student', 'faculty'])
                  ->default('student')
                  ->after('role_id');
            $table->enum('faculty_subtype', ['teacher', 'non_teacher', 'staff'])
                  ->nullable()
                  ->after('user_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['faculty_subtype', 'user_type']);
        });
    }
};
