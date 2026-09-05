<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class VisitorLog
 *
 * Stores anonymous daily unique visitor hits to the homepage for analytics.
 *
 * @package App\Models
 * @property int $id
 * @property string $ip_address
 * @property string|null $user_agent
 * @property string|null $url
 * @property string|null $method
 * @property string|null $referer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $browser_summary Human-readable browser and device string
 */
class VisitorLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'visitor_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ip_address',
        'user_agent',
        'url',
        'method',
        'referer',
    ];

    /**
     * Extract browser brand and device platform from HTTP User-Agent.
     *
     * @return string E.g. "Chrome (Desktop)" or "Mobile (Mobile)"
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
