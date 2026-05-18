<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('users', fn ($q) => $q->where('user_id', $userId));
    }

    public function hasMember(int $userId): bool
    {
        if ($this->relationLoaded('users')) {
            return $this->users->contains(fn (User $user) => $user->id === $userId);
        }

        return $this->users()->where('user_id', $userId)->exists();
    }

    public function displayName(?User $viewer = null): string
    {
        $viewer ??= auth()->user();

        if ($this->type === 'group') {
            return $this->name ?? 'Group Chat';
        }

        $other = $this->users->firstWhere('id', '!=', $viewer?->id);

        return $other?->name ?? 'Unknown';
    }

    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    public function isGroup(): bool
    {
        return $this->type === 'group';
    }
}
