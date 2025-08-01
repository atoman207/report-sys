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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
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
        ];
    }

    /**
     * Get the reports for this user.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Return default avatar if no custom avatar is set
        return asset('images/default-avatar.png');
    }

    /**
     * Check if user has a custom avatar.
     */
    public function hasCustomAvatar(): bool
    {
        return !empty($this->avatar);
    }

    /**
     * Get avatar display name for alt text.
     */
    public function getAvatarDisplayName(): string
    {
        return $this->name . "'s avatar";
    }

    /**
     * Get the user's last login time formatted.
     */
    public function getLastLoginFormattedAttribute(): string
    {
        if (!$this->last_login_at) {
            return '初回ログイン';
        }
        
        // Ensure last_login_at is a Carbon instance
        if (is_string($this->last_login_at)) {
            $this->last_login_at = \Carbon\Carbon::parse($this->last_login_at);
        }
        
        return $this->last_login_at->diffForHumans();
    }

    /**
     * Check if user has logged in before.
     */
    public function hasLoggedInBefore(): bool
    {
        return !is_null($this->last_login_at);
    }

    /**
     * Get user's login count (based on last_login_at updates).
     */
    public function getLoginCountAttribute(): int
    {
        // This is a simplified version - in a real app you might want a separate login_logs table
        return $this->hasLoggedInBefore() ? 1 : 0;
    }

    /**
     * Get user's activity status.
     */
    public function getActivityStatusAttribute(): string
    {
        if (!$this->last_login_at) {
            return 'inactive';
        }
        
        // Ensure last_login_at is a Carbon instance
        if (is_string($this->last_login_at)) {
            $this->last_login_at = \Carbon\Carbon::parse($this->last_login_at);
        }
        
        $daysSinceLastLogin = $this->last_login_at->diffInDays(now());
        
        if ($daysSinceLastLogin === 0) {
            return 'active_today';
        } elseif ($daysSinceLastLogin <= 7) {
            return 'active_recent';
        } elseif ($daysSinceLastLogin <= 30) {
            return 'active_month';
        } else {
            return 'inactive';
        }
    }
}
