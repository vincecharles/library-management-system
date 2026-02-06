<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('nationality', 50)->nullable();
            $table->text('biography')->nullable();
            $table->timestamps();
        });

        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('location', 100)->nullable();
            $table->string('contact', 50)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 20)->nullable()->index();
            $table->string('title');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->integer('publication_year')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('shelf_location', 50)->nullable();
            $table->integer('total_copies')->default(0);
            $table->integer('available_copies')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index('title');
        });

        Schema::create('book_authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade');
            $table->unique(['book_id', 'author_id']);
        });

        Schema::create('book_publishers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('publisher_id')->constrained('publishers')->onDelete('cascade');
            $table->unique(['book_id', 'publisher_id']);
        });

        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->string('accession_no', 20)->unique();
            $table->string('barcode', 50)->unique();
            $table->enum('condition_status', ['new', 'good', 'fair', 'poor', 'damaged', 'lost'])->default('new');
            $table->enum('status', ['available', 'borrowed', 'reserved', 'maintenance', 'lost', 'disposed'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
        Schema::dropIfExists('book_publishers');
        Schema::dropIfExists('book_authors');
        Schema::dropIfExists('books');
        Schema::dropIfExists('publishers');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('categories');
    }
};
