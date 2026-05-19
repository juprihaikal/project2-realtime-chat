<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_online',
        'last_seen',
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
            'last_seen'         => 'datetime',
            'is_online'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    /**
     * Mark user as online.
     */
    public function markAsOnline(): void
    {
        $this->update([
            'is_online' => true,
            'last_seen' => now(),
        ]);
    }

    /**
     * Mark user as offline.
     */
    public function markAsOffline(): void
    {
        $this->update([
            'is_online' => false,
            'last_seen' => now(),
        ]);
    }
}
