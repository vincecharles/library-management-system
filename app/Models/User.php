<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'username',
        'name',
        'email',
        'password',
        'profile_photo',
        'status',
        'failed_login_attempts',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'failed_login_attempts' => 'integer',
        ];
    }

    /**
     * Get the role that belongs to the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the role name attribute.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->role ? $this->role->name : '';
    }

    /**
     * Get the transactions issued by this user.
     */
    public function issuedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'issued_by');
    }

    /**
     * Get the transactions returned to this user.
     */
    public function returnedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'returned_to');
    }

    /**
     * Get the payments received by this user.
     */
    public function receivedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'received_by');
    }

    /**
     * Get the activity logs for this user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
