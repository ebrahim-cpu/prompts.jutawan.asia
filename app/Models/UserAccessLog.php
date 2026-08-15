<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessLog extends Model
{
    use HasFactory;

    protected $table = 'user_access_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'event_type',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'referer',
    ];

    /**
     * Relationship to User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted event type badge label.
     */
    public function getEventBadgeAttribute(): array
    {
        $type = strtoupper($this->event_type ?? 'LOGIN');
        if ($type === 'LOGOUT') {
            return [
                'label' => 'Log Keluar',
                'bg'    => 'bg-red-500/10 border-red-500/20 text-red-400',
                'icon'  => '🚪'
            ];
        }
        return [
            'label' => 'Log Masuk Berjaya',
            'bg'    => 'bg-green-500/10 border-green-500/20 text-green-400',
            'icon'  => '🔑'
        ];
    }

    /**
     * Get a human-readable browser & OS summary from user_agent.
     */
    public function getBrowserSummaryAttribute(): string
    {
        $agent = $this->user_agent ?? '';
        if (empty($agent)) {
            return 'Peranti Tidak Diketahui';
        }

        $platform = 'Desktop';
        if (preg_match('/mobile/i', $agent)) {
            $platform = 'Telefon Bimbit';
        } elseif (preg_match('/tablet|ipad/i', $agent)) {
            $platform = 'Tablet';
        }

        $browser = 'Pelayar Web';
        if (preg_match('/chrome/i', $agent) && !preg_match('/edg/i', $agent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/safari/i', $agent) && !preg_match('/chrome/i', $agent)) {
            $browser = 'Safari';
        } elseif (preg_match('/firefox/i', $agent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/edg/i', $agent)) {
            $browser = 'Edge';
        }

        return "{$browser} ({$platform})";
    }
}
