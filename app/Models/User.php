<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::ACTIVE)
                    ->whereNotNull('email_verified_at');
    }

    public function scopeSearchByName(Builder $query, ?string $name): Builder
    {
        if (!$name) return $query;
        return $query->where('name', 'like', "%{$name}%");
    }

    // Relations
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
                    ->select(['users.id', 'users.name', 'users.email', 'users.status']) 
                    ->withTimestamps();
    }
    
    public function friendOf()
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
                    ->select(['users.id', 'users.name', 'users.email', 'users.status']) 
                    ->withTimestamps();
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Helper methods
    public function isFriendWith(User $user): bool
    {
        return $this->friends()->where('friend_id', $user->id)->exists() ||
               $this->friendOf()->where('user_id', $user->id)->exists();
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE && $this->hasVerifiedEmail();
    }
}