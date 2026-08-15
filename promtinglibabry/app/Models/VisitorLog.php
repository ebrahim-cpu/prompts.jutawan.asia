<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    use HasFactory;

    protected $table = 'visitor_logs';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'url',
        'method',
        'referer',
    ];

    /**
     * Get a human-readable browser & OS summary from user_agent.
     */
    public function getBrowserSummaryAttribute(): string
    {
        $agent = $this->user_agent ?? '';
        if (empty($agent)) {
            return 'Unknown Device';
        }

        $platform = 'Desktop';
        if (preg_match('/mobile/i', $agent)) {
            $platform = 'Mobile';
        } elseif (preg_match('/tablet|ipad/i', $agent)) {
            $platform = 'Tablet';
        }

        $browser = 'Browser';
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
