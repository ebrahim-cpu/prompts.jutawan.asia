<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Class User
 *
 * Represents an authenticated user, subscriber, or system administrator.
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $google_id
 * @property string|null $avatar
 * @property string $role 'user' or 'admin'
 * @property string $tier 'free' or 'premium'
 * @property Carbon|null $subscription_starts_at
 * @property Carbon|null $premium_expires_at NULL denotes lifetime premium access
 * @property string|null $stripe_customer_id
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'google_id', 'avatar', 'role', 'tier', 'subscription_starts_at', 'premium_expires_at', 'stripe_customer_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast to native types.
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
     * Determine if the user currently holds active premium privileges.
     *
     * Rules:
     * 1. System administrators always have active premium access.
     * 2. Non-premium tier users return false.
     * 3. Premium tier with null expiration indicates lifetime access.
     * 4. Premium tier with a future expiration timestamp returns true.
     *
     * @return bool True if active premium or admin, false otherwise.
     */
    public function isPremiumActive(): bool
    {
        if ($this->role === 'admin') return true;
        if ($this->tier !== 'premium') return false;
        if (is_null($this->premium_expires_at)) return true; // lifetime
        return $this->premium_expires_at->isFuture();
    }

    /**
     * Check if the premium subscription is nearing expiration (within 30 days)
     * or has recently lapsed.
     *
     * @return bool
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
     * Check if the user's paid subscription has elapsed.
     *
     * @return bool True if expired, false otherwise.
     */
    public function isSubscriptionExpired(): bool
    {
        if ($this->role === 'admin' || is_null($this->premium_expires_at)) {
            return false;
        }
        return $this->premium_expires_at->isPast();
    }

    /**
     * Calculate remaining days until subscription expiry.
     *
     * @return int|null Days remaining, 0 if past, or null if lifetime.
     */
    public function daysUntilExpiry(): ?int
    {
        if (is_null($this->premium_expires_at)) return null;
        if ($this->premium_expires_at->isPast()) return 0;
        
        return now()->diffInDays($this->premium_expires_at->copy()->addDay());
    }

    /**
     * Automatically revert expired premium subscriptions back to the 'free' tier.
     *
     * Invoked automatically by CheckSubscriptionExpiry middleware during web requests.
     *
     * @return void
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
