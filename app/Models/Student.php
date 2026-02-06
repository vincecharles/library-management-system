<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_no',
        'library_card_no',
        'lrn',
        'first_name',
        'middle_name',
        'last_name',
        'grade_level_id',
        'section_id',
        'school_year',
        'contact_number',
        'email',
        'address',
        'guardian_name',
        'guardian_contact',
        'photo',
        'status',
        'max_books_allowed',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'max_books_allowed' => 'integer',
        ];
    }

    /**
     * Get the full name attribute (first_name + middle_name + last_name).
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $parts);
    }

    /**
     * Get the grade level that this student belongs to.
     */
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    /**
     * Get the section that this student belongs to.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the transactions for this student.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the fines for this student.
     */
    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * Get the payments for this student.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the reservations for this student.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
