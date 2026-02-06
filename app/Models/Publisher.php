<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Publisher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'location',
        'contact',
        'email',
    ];

    /**
     * Get the books by this publisher.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_publishers');
    }
}
