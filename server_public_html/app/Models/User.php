<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'google_id', 'avatar', 'role', 'tier', 'subscription_starts_at', 'premium_expires_at', 'stripe_customer_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'subscription_starts_at' => 'datetime',
            'premium_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has active premium subscription.
     */
    public function isPremiumActive(): bool
    {
        if ($this->role === 'admin') return true;
        if ($this->tier !== 'premium') return false;
        if (is_null($this->premium_expires_at)) return true; // lifetime
        return $this->premium_expires_at->isFuture();
    }

    /**
     * Check if subscription is expiring within 30 days (1 month) or has already expired for paid users.
     */
    public function isSubscriptionExpiringSoon(): bool
    {
        if ($this->role === 'admin' || is_null($this->premium_expires_at)) {
            return false;
        }

        if ($this->premium_expires_at->isPast()) {
            return true;
        }

        return $this->premium_expires_at->isFuture() && now()->diffInDays($this->premium_expires_at) <= 30;
    }

    /**
     * Check if subscription has already expired.
     */
    public function isSubscriptionExpired(): bool
    {
        if ($this->role === 'admin' || is_null($this->premium_expires_at)) {
            return false;
        }
        return $this->premium_expires_at->isPast();
    }

    /**
     * Get days until expiry.
     */
    public function daysUntilExpiry(): ?int
    {
        if (is_null($this->premium_expires_at)) return null;
        if ($this->premium_expires_at->isPast()) return 0;
        
        return now()->diffInDays($this->premium_expires_at->copy()->addDay()); // +1 because diffInDays heavily floors
    }

    /**
     * Auto revert to free if expired.
     */
    public function autoExpireSubscription(): void
    {
        if ($this->role === 'admin') return;
        
        if ($this->tier === 'premium' && $this->premium_expires_at && $this->premium_expires_at->isPast()) {
            $this->update([
                'tier' => 'free',
                'subscription_starts_at' => null,
                'premium_expires_at' => null,
            ]);
        }
    }
}
