<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no', 20)->unique();
            $table->foreignId('student_id')->constrained('students')->onDelete('restrict');
            $table->foreignId('book_copy_id')->constrained('book_copies')->onDelete('restrict');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'lost'])->default('borrowed');
            $table->enum('condition_on_borrow', ['new', 'good', 'fair', 'poor'])->default('good');
            $table->enum('condition_on_return', ['new', 'good', 'fair', 'poor', 'damaged', 'lost'])->nullable();
            $table->integer('renewals')->default(0);
            $table->foreignId('issued_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('returned_to')->nullable()->constrained('users')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index(['student_id', 'status']);
        });

        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('restrict');
            $table->enum('fine_type', ['overdue', 'lost', 'damaged'])->default('overdue');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'waived', 'partial'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fine_id')->constrained('fines')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'gcash', 'card'])->default('cash');
            $table->foreignId('received_by')->constrained('users')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->date('reservation_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'fulfilled', 'cancelled', 'expired'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('fines');
        Schema::dropIfExists('transactions');
    }
};
